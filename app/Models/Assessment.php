<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Assessment extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessments';

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
        'assessment_date',
        'status',
        'notes',
        'completed_at',
        'completed_by',
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
        'completed_at' => 'datetime',
    ];

    /**
     * Get the period that owns the assessment.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id');
    }

    /**
     * Get the group that owns the assessment.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(SnarsGroup::class, 'group_id');
    }

    /**
     * Get the chapter that owns the assessment.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(SnarsChapter::class, 'chapter_id');
    }

    /**
     * Get the standard that owns the assessment.
     */
    public function standard(): BelongsTo
    {
        return $this->belongsTo(SnarsStandard::class, 'standard_id');
    }

    /**
     * Get the scores for this assessment.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'assessment_id');
    }

    /**
     * Get the findings for this assessment.
     */
    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class, 'assessment_id');
    }

    /**
     * Get the user who completed the assessment.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
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
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include completed assessments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending assessments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in-progress assessments.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
