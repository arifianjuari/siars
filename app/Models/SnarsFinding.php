<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsFinding extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_findings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assessment_score_id',
        'finding_type',
        'description',
        'root_cause_analysis',
        'corrective_action',
        'target_completion_date',
        'status',
        'responsible_person',
        'evidence',
        'closed_date',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the assessment that owns the finding.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessment::class, 'assessment_id');
    }

    /**
     * Get the assessment score that owns the finding.
     */
    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentScore::class, 'assessment_score_id');
    }
    
    /**
     * Get the assessment element through the assessment score.
     */
    public function element()
    {
        return $this->assessmentScore->element();
    }

    /**
     * Get the user who created the finding.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the finding.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by assessment.
     */
    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    /**
     * Scope a query to filter by element through assessment score.
     */
    public function scopeForElement($query, $elementId)
    {
        return $query->whereHas('assessmentScore', function ($query) use ($elementId) {
            $query->where('element_id', $elementId);
        });
    }

    /**
     * Scope a query to filter by finding type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('finding_type', $type);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include resolved findings.
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Scope a query to only include unresolved findings.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope a query to only include findings due by a certain date.
     */
    public function scopeDueBy($query, $date)
    {
        return $query->where('due_date', '<=', $date);
    }

    /**
     * Scope a query to only include overdue findings.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('resolved_at')
                    ->where('due_date', '<', now()->format('Y-m-d'));
    }
}
