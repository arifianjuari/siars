<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class MonitoringResult extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'monitoring_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_id',
        'element_id',
        'monitoring_date',
        'status',
        'findings',
        'action_taken',
        'recommendation',
        'completed_by',
        'completed_at',
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
        'completed_at' => 'datetime',
    ];

    /**
     * Get the schedule that owns the monitoring result.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MonitoringSchedule::class, 'schedule_id');
    }

    /**
     * Get the assessment element that owns the monitoring result.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the user who completed the monitoring.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
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
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include compliant results.
     */
    public function scopeCompliant($query)
    {
        return $query->where('status', 'compliant');
    }

    /**
     * Scope a query to only include non-compliant results.
     */
    public function scopeNonCompliant($query)
    {
        return $query->where('status', 'non_compliant');
    }

    /**
     * Scope a query to only include partially compliant results.
     */
    public function scopePartiallyCompliant($query)
    {
        return $query->where('status', 'partially_compliant');
    }

    /**
     * Get the period through the schedule relationship.
     */
    public function period()
    {
        return $this->schedule->period;
    }
}
