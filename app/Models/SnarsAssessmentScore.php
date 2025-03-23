<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsAssessmentScore extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_assessment_scores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assessment_id',
        'element_id',
        'score',
        'notes',
        'evidence',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'float',
    ];

    /**
     * Get the assessment that owns the score.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessment::class, 'assessment_id');
    }

    /**
     * Get the assessment element that owns the score.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the user who created the assessment score.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the assessment score.
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
     * Scope a query to filter by element.
     */
    public function scopeForElement($query, $elementId)
    {
        return $query->where('element_id', $elementId);
    }

    /**
     * Scope a query to filter by score range.
     */
    public function scopeWithScoreBetween($query, $min, $max)
    {
        return $query->whereBetween('score', [$min, $max]);
    }
    
    /**
     * Get the findings for this assessment score.
     */
    public function findings()
    {
        return $this->hasMany(SnarsFinding::class, 'assessment_score_id');
    }
}
