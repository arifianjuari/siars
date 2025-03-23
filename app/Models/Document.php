<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Document extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'required_document_id',
        'title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'version',
        'is_current_version',
        'notes',
        'document_date',
        'expiry_date',
        'status',
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
        'is_current_version' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer',
        'document_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the required document that owns the document.
     */
    public function requiredDocument(): BelongsTo
    {
        return $this->belongsTo(RequiredDocument::class, 'required_document_id');
    }

    /**
     * Get the assessment elements mapped to this document.
     */
    public function assessmentElements(): BelongsToMany
    {
        return $this->belongsToMany(SnarsAssessmentElement::class, 'document_element_mappings', 'document_id', 'assessment_element_id')
                    ->withPivot('notes', 'is_primary')
                    ->withTimestamps();
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
     * Scope a query to only include current versions.
     */
    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter documents that are expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $today = now();
        $future = now()->addDays($days);
        return $query->whereNotNull('expiry_date')
                     ->whereBetween('expiry_date', [$today, $future]);
    }

    /**
     * Scope a query to filter expired documents.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', now());
    }

    /**
     * Get the document type through the required document relationship.
     */
    public function documentType()
    {
        return $this->requiredDocument->documentType;
    }
}
