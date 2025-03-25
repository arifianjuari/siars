<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class Document extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

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
        'tenant_id',
        'document_number',
        'document_type',
        'subject',
        'content',
        'reference_number',
        'document_date',
        'status',
        'qr_code_path',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'document_date' => 'date',
    ];

    /**
     * Get the memo associated with the document.
     */
    public function memo(): HasOne
    {
        return $this->hasOne(Memo::class);
    }

    /**
     * Get the invitation associated with the document.
     */
    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class);
    }

    /**
     * Get the meeting minutes associated with the document.
     */
    public function meetingMinutes(): HasOne
    {
        return $this->hasOne(MeetingMinute::class);
    }

    /**
     * Get the histories for the document.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    /**
     * Get the attachments for the document.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    /**
     * Get the recipients for the document.
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(DocumentRecipient::class);
    }

    /**
     * Get the signatures for the document.
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    /**
     * Get the notifications for the document.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(DocumentNotification::class);
    }

    /**
     * Get the tenant that owns the document.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the document.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the document.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include documents with the specified type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to only include documents with the specified status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include documents for the current tenant.
     */
    public function scopeForCurrentTenant($query)
    {
        return $query->where('tenant_id', Auth::user()->tenant_id);
    }

    /**
     * Generate document number based on type and tenant.
     */
    public static function generateDocumentNumber($type, $tenantId)
    {
        $prefix = '';
        switch ($type) {
            case 'nota_dinas_masuk':
                $prefix = 'NDM';
                break;
            case 'nota_dinas_keluar':
                $prefix = 'NDK';
                break;
            case 'undangan':
                $prefix = 'UND';
                break;
            case 'notulensi':
                $prefix = 'NOT';
                break;
            default:
                $prefix = 'DOC';
        }

        $year = date('Y');
        $month = date('m');

        $lastDocument = self::where('tenant_id', $tenantId)
            ->where('document_type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('document_number', 'desc')
            ->first();

        $number = 1;
        if ($lastDocument) {
            $parts = explode('/', $lastDocument->document_number);
            if (count($parts) >= 3) {
                $number = (int)$parts[0] + 1;
            }
        }

        $tenant = Tenant::find($tenantId);
        $tenantCode = $tenant ? substr($tenant->code, 0, 3) : 'TNT';

        return sprintf('%03d/%s/%s/%s/%s', $number, $prefix, $tenantCode, $month, $year);
    }
}
