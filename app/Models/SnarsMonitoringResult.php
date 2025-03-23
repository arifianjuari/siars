<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsMonitoringResult extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_monitoring_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_id',
        'monitoring_date',
        'result',
        'notes',
        'evidence',
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
        'monitoring_date' => 'date',
    ];

    /**
     * Get the monitoring schedule that owns the result.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SnarsMonitoringSchedule::class, 'schedule_id');
    }

    /**
     * Get the user who created the monitoring result.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the monitoring result.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by schedule.
     */
    public function scopeForSchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('monitoring_date', [$startDate, $endDate]);
    }

    /**
     * Get the assessment element through the schedule relationship.
     */
    public function element()
    {
        return $this->schedule->element;
    }

    /**
     * Get the department through the schedule relationship.
     */
    public function department()
    {
        return $this->schedule->department;
    }
}
