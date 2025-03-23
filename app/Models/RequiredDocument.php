<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class RequiredDocument extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'required_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assessment_element_id',
        'document_type_id',
        'name',
        'description',
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
     * Get the assessment element that owns the required document.
     */
    public function assessmentElement(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'assessment_element_id');
    }

    /**
     * Get the document type that owns the required document.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    /**
     * Get the documents for this required document.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'required_document_id');
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
     * Scope a query to only include optional documents.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }
}
