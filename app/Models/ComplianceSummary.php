<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class ComplianceSummary extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'compliance_summary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_id',
        'entity_id',
        'entity_type',
        'total_elements',
        'compliant_count',
        'non_compliant_count',
        'partially_compliant_count',
        'not_assessed_count',
        'compliance_percentage',
        'last_assessment_date',
        'next_assessment_date',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_elements' => 'integer',
        'compliant_count' => 'integer',
        'non_compliant_count' => 'integer',
        'partially_compliant_count' => 'integer',
        'not_assessed_count' => 'integer',
        'compliance_percentage' => 'float',
        'last_assessment_date' => 'date',
        'next_assessment_date' => 'date',
    ];

    /**
     * Get the period that owns the compliance summary.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id');
    }

    /**
     * Get the entity that owns the compliance summary.
     * This is a polymorphic relationship.
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the compliance summary.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the compliance summary.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by entity type.
     */
    public function scopeEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include high compliance.
     */
    public function scopeHighCompliance($query, $threshold = 90)
    {
        return $query->where('compliance_percentage', '>=', $threshold);
    }

    /**
     * Scope a query to only include medium compliance.
     */
    public function scopeMediumCompliance($query, $min = 70, $max = 90)
    {
        return $query->whereBetween('compliance_percentage', [$min, $max]);
    }

    /**
     * Scope a query to only include low compliance.
     */
    public function scopeLowCompliance($query, $threshold = 70)
    {
        return $query->where('compliance_percentage', '<', $threshold);
    }
}
