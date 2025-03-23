<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsDocument extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'department_id',
        'document_type_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'version',
        'status',
        'approval_date',
        'expiry_date',
        'notes',
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
        'version' => 'float',
        'approval_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the tenant that owns the document.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the department that owns the document.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the document type for this document.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(SnarsDocumentType::class, 'document_type_id');
    }

    /**
     * Get the assessment elements mapped to this document.
     */
    public function assessmentElements(): BelongsToMany
    {
        return $this->belongsToMany(SnarsAssessmentElement::class, 'snars_document_element_mappings', 'document_id', 'element_id')
                    ->withPivot('notes', 'is_primary')
                    ->withTimestamps();
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
     * Scope a query to filter by document status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by document type.
     */
    public function scopeOfType($query, $documentTypeId)
    {
        return $query->where('document_type_id', $documentTypeId);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to filter documents that are expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $date = now()->addDays($days);
        return $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<=', $date)
                    ->whereDate('expiry_date', '>=', now());
    }

    /**
     * Scope a query to filter expired documents.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', now());
    }
}
