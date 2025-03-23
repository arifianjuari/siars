<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Module extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the tenants that have this module.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot('is_active', 'settings', 'approved_by', 'approved_at')
            ->withTimestamps();
    }

    /**
     * Get the role permissions for this module.
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Get the activation requests for this module.
     */
    public function activationRequests(): HasMany
    {
        return $this->hasMany(ModuleActivationRequest::class);
    }

    /**
     * Get the user who created the module.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the module.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Mendapatkan semua role yang memiliki akses ke modul ini
     */
    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'role_module_permissions')
            ->withPivot('can_view', 'can_create', 'can_edit', 'can_delete', 'can_approve', 'can_activate')
            ->withTimestamps();
    }
}
