<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsMonitoringSchedule extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_monitoring_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'element_id',
        'department_id',
        'frequency',
        'start_date',
        'end_date',
        'status',
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
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the assessment element that owns the monitoring schedule.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the department that owns the monitoring schedule.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the monitoring results for this schedule.
     */
    public function results(): HasMany
    {
        return $this->hasMany(SnarsMonitoringResult::class, 'schedule_id');
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
     * Scope a query to filter by element.
     */
    public function scopeForElement($query, $elementId)
    {
        return $query->where('element_id', $elementId);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by frequency.
     */
    public function scopeWithFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Scope a query to filter active schedules.
     */
    public function scopeActive($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->where('end_date', '>=', $today)
                          ->orWhereNull('end_date');
                    });
    }

    /**
     * Scope a query to filter upcoming schedules.
     */
    public function scopeUpcoming($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '>', $today);
    }

    /**
     * Scope a query to filter expired schedules.
     */
    public function scopeExpired($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('end_date', '<', $today);
    }
}
