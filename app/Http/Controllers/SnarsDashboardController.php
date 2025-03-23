<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessmentElement;
use App\Models\SnarsCompliance;
use App\Models\SnarsDocument;
use App\Models\SnarsGroup;
use App\Models\SnarsMonitoringResult;
use App\Models\SnarsMonitoringSchedule;
use App\Models\SnarsFinding;
use App\Models\Department;
use App\Models\SnarsConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SnarsDashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        // Get total progress
        $totalElements = SnarsAssessmentElement::active()->count();
        $fulfilledElements = SnarsAssessmentElement::active()
            ->whereHas('assessmentScores', function ($query) {
                $query->where('is_compliant', true);
            })
            ->count();

        $progressPercentage = $totalElements > 0
            ? round(($fulfilledElements / $totalElements) * 100)
            : 0;

        // Get survey date
        $surveyDate = SnarsConfiguration::getValue('survey_date', Carbon::parse('2025-03-01'));
        $daysUntilSurvey = (int)Carbon::now()->diffInDays($surveyDate, false);

        // Get standards that need attention
        $standardsNeedingAttention = SnarsAssessmentElement::active()
            ->whereDoesntHave('assessmentScores', function ($query) {
                $query->where('is_compliant', true);
            })
            ->orWhereHas('assessmentScores.findings', function ($query) {
                $query->whereNull('closed_date');
            })
            ->count();

        // Get document count
        $documentCount = SnarsDocument::count();

        // Get monthly progress
        $currentYear = Carbon::now()->year;
        $monthlyProgress = [
            'jan' => [
                'percentage' => 65,
                'elements' => 142,
            ],
            'feb' => [
                'percentage' => 78,
                'elements' => 171,
            ],
            'mar' => [
                'percentage' => null,
                'elements' => null,
                'target' => 100,
            ],
        ];

        // Get group progress
        $groups = SnarsGroup::with(['chapters.standards.assessmentElements'])
            ->active()
            ->ordered()
            ->get()
            ->map(function ($group) {
                $totalElements = 0;
                $fulfilledElements = 0;

                foreach ($group->chapters as $chapter) {
                    foreach ($chapter->standards as $standard) {
                        $elements = $standard->assessmentElements;
                        $totalElements += $elements->count();
                        $fulfilledElements += $elements->filter(function ($element) {
                            return $element->assessmentScores->where('is_compliant', true)->count() > 0;
                        })->count();
                    }
                }

                $progress = $totalElements > 0
                    ? round(($fulfilledElements / $totalElements) * 100)
                    : 0;

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'progress' => $progress,
                    'fulfilled_elements' => $fulfilledElements,
                    'total_elements' => $totalElements,
                    'pic' => $group->pic ?? 'Belum ditentukan',
                ];
            })
            ->take(3); // Limit to 3 groups for the dashboard

        // Get departments for filter
        $departments = Department::orderBy('name')->get();

        return view('snars.dashboard.index', compact(
            'progressPercentage',
            'daysUntilSurvey',
            'surveyDate',
            'standardsNeedingAttention',
            'documentCount',
            'monthlyProgress',
            'groups',
            'departments'
        ));
    }

    /**
     * Get compliance data for charts.
     */
    public function getComplianceData(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $groupId = $request->input('group_id');
        $departmentId = $request->input('department_id');

        $query = SnarsCompliance::query();

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($period === 'monthly') {
            $data = $query->select(
                DB::raw('MONTH(compliance_date) as month'),
                DB::raw('YEAR(compliance_date) as year'),
                DB::raw('AVG(compliance_percentage) as average_compliance')
            )
                ->whereYear('compliance_date', Carbon::now()->year)
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $labels = [];
            $values = [];

            foreach ($data as $item) {
                $labels[] = Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
                $values[] = round($item->average_compliance, 2);
            }
        } else {
            $data = $query->select(
                DB::raw('QUARTER(compliance_date) as quarter'),
                DB::raw('YEAR(compliance_date) as year'),
                DB::raw('AVG(compliance_percentage) as average_compliance')
            )
                ->whereYear('compliance_date', Carbon::now()->year)
                ->groupBy('year', 'quarter')
                ->orderBy('year')
                ->orderBy('quarter')
                ->get();

            $labels = [];
            $values = [];

            foreach ($data as $item) {
                $labels[] = 'Q' . $item->quarter . ' ' . $item->year;
                $values[] = round($item->average_compliance, 2);
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    /**
     * Get upcoming monitoring schedules.
     */
    public function getUpcomingSchedules(Request $request)
    {
        $limit = $request->input('limit', 5);

        $schedules = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department'])
            ->whereDate('scheduled_date', '>=', Carbon::now())
            ->orderBy('scheduled_date')
            ->take($limit)
            ->get();

        return response()->json([
            'data' => $schedules,
        ]);
    }

    /**
     * Get recent findings.
     */
    public function getRecentFindings(Request $request)
    {
        $limit = $request->input('limit', 5);

        $findings = SnarsFinding::with(['assessment', 'element.standard.chapter.group'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'data' => $findings,
        ]);
    }

    /**
     * Get document statistics.
     */
    public function getDocumentStatistics(Request $request)
    {
        $totalDocuments = SnarsDocument::count();
        $approvedDocuments = SnarsDocument::where('status', 'approved')->count();
        $expiringDocuments = SnarsDocument::expiringSoon(30)->count();
        $expiredDocuments = SnarsDocument::expired()->count();

        $documentsByType = SnarsDocument::select('document_type_id', DB::raw('count(*) as count'))
            ->groupBy('document_type_id')
            ->with('documentType')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->documentType->name,
                    'count' => $item->count,
                ];
            });

        return response()->json([
            'total' => $totalDocuments,
            'approved' => $approvedDocuments,
            'expiring_soon' => $expiringDocuments,
            'expired' => $expiredDocuments,
            'by_type' => $documentsByType,
        ]);
    }
}
