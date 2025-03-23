<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class TenantModule extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_modules';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['tenant_id', 'module_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'module_id',
        'is_active',
        'settings',
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
        'is_active' => 'boolean',
        'settings' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the module.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the module.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the user who created the tenant module.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the tenant module.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the module activation.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include active tenant modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive tenant modules.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Activate the module for the tenant.
     *
     * @param int $approvedBy ID of the user who approved the activation
     * @param array $settings Optional settings for the module
     * @return bool
     */
    public function activate(int $approvedBy, array $settings = null): bool
    {
        $this->is_active = true;
        $this->approved_by = $approvedBy;
        $this->approved_at = now();

        if ($settings !== null) {
            $this->settings = $settings;
        }

        return $this->save();
    }

    /**
     * Deactivate the module for the tenant.
     *
     * @param int $updatedBy ID of the user who deactivated the module
     * @return bool
     */
    public function deactivate(int $updatedBy): bool
    {
        $this->is_active = false;
        $this->updated_by = $updatedBy;

        return $this->save();
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $value = $this->original[$keyName] ?? $this->getAttribute($keyName);
            $query->where($keyName, '=', $value);
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (is_array($keyName)) {
            throw new \Exception('getKeyForSaveQuery tidak dapat dipanggil dengan keyName array');
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        $keyName = $this->getKeyName();

        if (is_array($keyName)) {
            $attributes = [];
            foreach ($keyName as $key) {
                $attributes[$key] = $this->getAttribute($key);
            }
            return $attributes;
        }

        return $this->getAttribute($keyName);
    }
}
