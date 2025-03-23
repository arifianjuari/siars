<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsCompliance extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_compliance_summary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'period_id',
        'department_id',
        'group_id',
        'chapter_id',
        'standard_id',
        'total_elements',
        'compliant_elements',
        'compliance_percentage',
        'status',
        'report_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_elements' => 'integer',
        'compliant_elements' => 'integer',
        'compliance_percentage' => 'float',
        'report_date' => 'date',
    ];

    /**
     * Get the tenant that owns the compliance summary.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the assessment period that owns the compliance summary.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentPeriod::class, 'period_id');
    }

    /**
     * Get the department that owns the compliance summary.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the group that owns the compliance summary.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(SnarsGroup::class, 'group_id');
    }

    /**
     * Get the chapter that owns the compliance summary.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(SnarsChapter::class, 'chapter_id');
    }

    /**
     * Get the standard that owns the compliance summary.
     */
    public function standard(): BelongsTo
    {
        return $this->belongsTo(SnarsStandard::class, 'standard_id');
    }

    /**
     * Get the user who created the compliance summary.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the compliance summary.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to filter by period.
     */
    public function scopeInPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope a query to filter by chapter.
     */
    public function scopeInChapter($query, $chapterId)
    {
        return $query->where('chapter_id', $chapterId);
    }

    /**
     * Scope a query to filter by standard.
     */
    public function scopeInStandard($query, $standardId)
    {
        return $query->where('standard_id', $standardId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by compliance percentage range.
     */
    public function scopeWithCompliancePercentageBetween($query, $min, $max)
    {
        return $query->whereBetween('compliance_percentage', [$min, $max]);
    }

    /**
     * Scope a query to filter by report date range.
     */
    public function scopeWithReportDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }
}
