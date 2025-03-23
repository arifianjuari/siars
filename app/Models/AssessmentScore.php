<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AssessmentScore extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessment_scores';

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
        'recommendation',
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
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    /**
     * Get the assessment element that owns the score.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the user who created the score.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the score.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by score range.
     */
    public function scopeScoreRange($query, $min, $max)
    {
        return $query->whereBetween('score', [$min, $max]);
    }

    /**
     * Scope a query to only include passing scores.
     */
    public function scopePassing($query, $threshold = 0.7)
    {
        return $query->where('score', '>=', $threshold);
    }

    /**
     * Scope a query to only include failing scores.
     */
    public function scopeFailing($query, $threshold = 0.7)
    {
        return $query->where('score', '<', $threshold);
    }
}
