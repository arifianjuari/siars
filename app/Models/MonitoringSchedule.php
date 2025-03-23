<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class MonitoringSchedule extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'monitoring_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_id',
        'group_id',
        'chapter_id',
        'standard_id',
        'element_id',
        'title',
        'description',
        'scheduled_date',
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
        'scheduled_date' => 'date',
    ];

    /**
     * Get the period that owns the monitoring schedule.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id');
    }

    /**
     * Get the group that owns the monitoring schedule.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(SnarsGroup::class, 'group_id');
    }

    /**
     * Get the chapter that owns the monitoring schedule.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(SnarsChapter::class, 'chapter_id');
    }

    /**
     * Get the standard that owns the monitoring schedule.
     */
    public function standard(): BelongsTo
    {
        return $this->belongsTo(SnarsStandard::class, 'standard_id');
    }

    /**
     * Get the assessment element that owns the monitoring schedule.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the monitoring results for this schedule.
     */
    public function results(): HasMany
    {
        return $this->hasMany(MonitoringResult::class, 'schedule_id');
    }

    /**
     * Get the user who created the monitoring schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the monitoring schedule.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending schedules.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed schedules.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include upcoming schedules.
     */
    public function scopeUpcoming($query, $days = 7)
    {
        $today = now()->format('Y-m-d');
        $future = now()->addDays($days)->format('Y-m-d');
        return $query->where('status', '!=', 'completed')
                     ->whereBetween('scheduled_date', [$today, $future]);
    }

    /**
     * Scope a query to only include overdue schedules.
     */
    public function scopeOverdue($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('status', '!=', 'completed')
                     ->where('scheduled_date', '<', $today);
    }
}
