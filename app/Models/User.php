<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\HasModulePermissions;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, \OwenIt\Auditing\Auditable, HasModulePermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'employee_id',
        'phone',
        'position',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Default attribute values
     * 
     * @var array
     */
    protected $attributes = [
        'is_active' => false,
    ];

    /**
     * Get the tenant that the user belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the tenant access entries for superadmin.
     */
    public function tenantAccess(): HasMany
    {
        return $this->hasMany(SuperadminTenantAccess::class);
    }

    /**
     * Get all accessible tenants for this user.
     */
    public function accessibleTenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'superadmin_tenant_access')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Get the default tenant for this superadmin.
     */
    public function defaultTenant()
    {
        return $this->accessibleTenants()
            ->wherePivot('is_default', true)
            ->first();
    }

    /**
     * Get the role management logs created by this user.
     */
    public function roleManagementLogs(): HasMany
    {
        return $this->hasMany(UserRoleManagementLog::class, 'admin_id');
    }

    /**
     * Get the role changes for this user.
     */
    public function roleChanges(): HasMany
    {
        return $this->hasMany(UserRoleManagementLog::class, 'target_user_id');
    }

    /**
     * Get the module activation requests created by this user.
     */
    public function moduleActivationRequests(): HasMany
    {
        return $this->hasMany(ModuleActivationRequest::class, 'requested_by');
    }

    /**
     * Get the module activation requests processed by this user.
     */
    public function processedModuleRequests(): HasMany
    {
        return $this->hasMany(ModuleActivationRequest::class, 'processed_by');
    }

    /**
     * Get the tenant modules approved by this user.
     */
    public function approvedModules(): HasMany
    {
        return $this->hasMany(TenantModule::class, 'approved_by');
    }

    /**
     * Check if the user is a superadmin.
     */
    public function isSuperadmin(): bool
    {
        return $this->hasRole('Superadmin');
    }

    /**
     * Check if the user is a tenant admin.
     */
    public function isTenantAdmin(): bool
    {
        return $this->hasRole('TenantAdmin');
    }

    /**
     * Get tenant module permissions for this user.
     */
    public function tenantModulePermissions()
    {
        return $this->hasRole('TenantAdmin') ? TenantModule::where('tenant_id', $this->tenant_id) : null;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
