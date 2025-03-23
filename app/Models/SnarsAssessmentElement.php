<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsAssessmentElement extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_assessment_elements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'standard_id',
        'code',
        'description',
        'assessment_guide',
        'evidence_requirements',
        'scoring_method',
        'weight',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'weight' => 'integer',
    ];

    /**
     * Get the standard that owns the assessment element.
     */
    public function standard(): BelongsTo
    {
        return $this->belongsTo(SnarsStandard::class, 'standard_id');
    }

    /**
     * Get the required documents for the assessment element.
     */
    public function requiredDocuments(): HasMany
    {
        return $this->hasMany(SnarsRequiredDocument::class, 'element_id');
    }

    /**
     * Get the documents mapped to this assessment element.
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(SnarsDocument::class, 'snars_document_element_mappings', 'element_id', 'document_id')
                    ->withPivot('notes', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * Get the assessment scores for this element.
     */
    public function assessmentScores(): HasMany
    {
        return $this->hasMany(SnarsAssessmentScore::class, 'element_id');
    }

    /**
     * Get the monitoring schedules for this element.
     */
    public function monitoringSchedules(): HasMany
    {
        return $this->hasMany(SnarsMonitoringSchedule::class, 'element_id');
    }

    /**
     * Get the findings for this element through assessment scores.
     */
    public function findings()
    {
        return $this->hasManyThrough(
            SnarsFinding::class,
            SnarsAssessmentScore::class,
            'element_id', // Foreign key on SnarsAssessmentScore table...
            'assessment_score_id', // Foreign key on SnarsFinding table...
            'id', // Local key on SnarsAssessmentElement table...
            'id' // Local key on SnarsAssessmentScore table...
        );
    }

    /**
     * Get the user who created the assessment element.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the assessment element.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active assessment elements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the 'order' field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the chapter through the standard relationship.
     */
    public function chapter()
    {
        return $this->standard->chapter;
    }

    /**
     * Get the group through the standard and chapter relationships.
     */
    public function group()
    {
        return $this->standard->chapter->group;
    }
}
