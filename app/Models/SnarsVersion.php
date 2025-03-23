<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsVersion extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version_number',
        'name',
        'description',
        'release_date',
        'is_active',
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
        'version_number' => 'float',
        'release_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the updates for this version.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(SnarsUpdate::class, 'version_id');
    }

    /**
     * Get the user who created the version.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the version.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active versions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by version number in descending order.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('version_number');
    }

    /**
     * Scope a query to filter by release date range.
     */
    public function scopeReleasedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('release_date', [$startDate, $endDate]);
    }

    /**
     * Get the latest active version.
     */
    public static function getLatestActive()
    {
        return static::active()->latestFirst()->first();
    }
}
