<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsRequiredDocument extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_required_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'element_id',
        'document_type_id',
        'name',
        'description',
        'requirements',
        'is_mandatory',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    /**
     * Get the assessment element that requires this document.
     */
    public function assessmentElement(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'element_id');
    }

    /**
     * Get the document type for this required document.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(SnarsDocumentType::class, 'document_type_id');
    }

    /**
     * Get the user who created the required document.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the required document.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include mandatory documents.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Get the standard through the assessment element relationship.
     */
    public function standard()
    {
        return $this->assessmentElement->standard;
    }

    /**
     * Get the chapter through the assessment element relationship.
     */
    public function chapter()
    {
        return $this->assessmentElement->chapter;
    }

    /**
     * Get the group through the assessment element relationship.
     */
    public function group()
    {
        return $this->assessmentElement->group;
    }
}
