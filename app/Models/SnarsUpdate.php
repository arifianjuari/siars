<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsUpdate extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_updates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version_id',
        'title',
        'description',
        'update_type',
        'affected_components',
        'is_major',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'affected_components' => 'json',
        'is_major' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the version that owns the update.
     */
    public function version(): BelongsTo
    {
        return $this->belongsTo(SnarsVersion::class, 'version_id');
    }

    /**
     * Get the user who created the update.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the update.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include major updates.
     */
    public function scopeMajor($query)
    {
        return $query->where('is_major', true);
    }

    /**
     * Scope a query to only include minor updates.
     */
    public function scopeMinor($query)
    {
        return $query->where('is_major', false);
    }

    /**
     * Scope a query to filter by update type.
     */
    public function scopeOfType($query, $updateType)
    {
        return $query->where('update_type', $updateType);
    }

    /**
     * Scope a query to filter by affected component.
     */
    public function scopeAffectingComponent($query, $component)
    {
        return $query->whereJsonContains('affected_components', $component);
    }

    /**
     * Scope a query to order by published date in descending order.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at');
    }
}
