<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class ModuleActivationRequest extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_activation_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'module_id',
        'requested_by',
        'status',
        'notes',
        'requested_at',
        'processed_by',
        'processed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the tenant associated with this request.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the module associated with this request.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the user who requested the activation.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who processed the request.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Approve the activation request and activate the module.
     *
     * @param int $processedBy ID of the user who approved the request
     * @param string|null $notes Optional notes about the approval
     * @return bool
     */
    public function approve(int $processedBy, ?string $notes = null): bool
    {
        // Update the request status
        $this->status = 'approved';
        $this->processed_by = $processedBy;
        $this->processed_at = now();

        if ($notes) {
            $this->notes = $notes;
        }

        if (!$this->save()) {
            return false;
        }

        // Activate the module
        $tenantModule = TenantModule::where('tenant_id', $this->tenant_id)
            ->where('module_id', $this->module_id)
            ->first();

        if (!$tenantModule) {
            // Create the tenant module relationship if it doesn't exist
            $tenantModule = new TenantModule();
            $tenantModule->tenant_id = $this->tenant_id;
            $tenantModule->module_id = $this->module_id;
            $tenantModule->created_by = $processedBy;
        }

        return $tenantModule->activate($processedBy);
    }

    /**
     * Reject the activation request.
     *
     * @param int $processedBy ID of the user who rejected the request
     * @param string|null $notes Optional notes about the rejection
     * @return bool
     */
    public function reject(int $processedBy, ?string $notes = null): bool
    {
        $this->status = 'rejected';
        $this->processed_by = $processedBy;
        $this->processed_at = now();

        if ($notes) {
            $this->notes = $notes;
        }

        return $this->save();
    }

    /**
     * Create a new module activation request.
     *
     * @param string $tenantId The tenant ID
     * @param int $moduleId The module ID
     * @param int $requestedBy ID of the user making the request
     * @param string|null $notes Optional notes about the request
     * @return self
     */
    public static function createRequest(string $tenantId, int $moduleId, int $requestedBy, ?string $notes = null): self
    {
        // Check if there's already a pending request
        $existingRequest = static::where('tenant_id', $tenantId)
            ->where('module_id', $moduleId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return $existingRequest;
        }

        // Create new request
        $request = new static();
        $request->tenant_id = $tenantId;
        $request->module_id = $moduleId;
        $request->requested_by = $requestedBy;
        $request->status = 'pending';
        $request->requested_at = now();

        if ($notes) {
            $request->notes = $notes;
        }

        $request->save();

        return $request;
    }
}
