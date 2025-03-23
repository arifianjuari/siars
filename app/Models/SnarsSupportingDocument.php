<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsSupportingDocument extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_supporting_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'version',
        'status',
        'rejection_reason',
        'assessment_element_id',
        'parent_id',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the assessment element that owns the document.
     */
    public function assessmentElement(): BelongsTo
    {
        return $this->belongsTo(SnarsAssessmentElement::class, 'assessment_element_id');
    }

    /**
     * Get the parent document (previous version).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SnarsSupportingDocument::class, 'parent_id');
    }

    /**
     * Get the child documents (newer versions).
     */
    public function children()
    {
        return $this->hasMany(SnarsSupportingDocument::class, 'parent_id');
    }

    /**
     * Get the user who approved the document.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created the document.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the document.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include documents with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include draft documents.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include documents under review.
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', 'review');
    }

    /**
     * Scope a query to only include approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected documents.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include the latest version of each document.
     */
    public function scopeLatestVersion($query)
    {
        return $query->whereNotIn('id', function ($query) {
            $query->select('parent_id')
                  ->from('snars_supporting_documents')
                  ->whereNotNull('parent_id');
        });
    }

    /**
     * Check if the document is a PDF.
     */
    public function isPdf()
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * Check if the document is an image.
     */
    public function isImage()
    {
        return str_starts_with($this->file_type, 'image/');
    }

    /**
     * Get the URL for the document.
     */
    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }
}
