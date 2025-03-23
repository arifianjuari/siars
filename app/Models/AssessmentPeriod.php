<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class AssessmentPeriod extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessment_periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the assessments for this period.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'period_id');
    }

    /**
     * Get the monitoring schedules for this period.
     */
    public function monitoringSchedules(): HasMany
    {
        return $this->hasMany(MonitoringSchedule::class, 'period_id');
    }

    /**
     * Get the compliance summaries for this period.
     */
    public function complianceSummaries(): HasMany
    {
        return $this->hasMany(ComplianceSummary::class, 'period_id');
    }

    /**
     * Get the user who created the assessment period.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the assessment period.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active periods.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed periods.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include current periods (active and date range includes today).
     */
    public function scopeCurrent($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('status', 'active')
                     ->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    /**
     * Scope a query to only include upcoming periods.
     */
    public function scopeUpcoming($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '>', $today);
    }
}
