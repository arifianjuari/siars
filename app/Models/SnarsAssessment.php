<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsAssessment extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_assessments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_id',
        'department_id',
        'assessment_date',
        'status',
        'notes',
        'overall_score',
        'is_completed',
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
        'assessment_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'overall_score' => 'float',
    ];

    /**
     * Get the assessment period that owns the assessment.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentPeriod::class, 'period_id');
    }

    /**
     * Get the department that owns the assessment.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the assessment scores for this assessment.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(SnarsAssessmentScore::class, 'assessment_id');
    }

    /**
     * Get the findings for this assessment.
     */
    public function findings(): HasMany
    {
        return $this->hasMany(SnarsFinding::class, 'assessment_id');
    }

    /**
     * Get the user who created the assessment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the assessment.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by assessment period.
     */
    public function scopeInPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    /**
     * Scope a query to only include completed assessments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope a query to only include pending assessments.
     */
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Get the tenant through the department relationship.
     */
    public function tenant()
    {
        return $this->department->tenant;
    }
}
