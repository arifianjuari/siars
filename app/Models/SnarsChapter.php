<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsChapter extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_chapters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'snars_group_id',
        'group_id',
        'code',
        'title',
        'description',
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
     * Get the group that owns the chapter.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(SnarsGroup::class, 'snars_group_id');
    }

    /**
     * Get the standards for the chapter.
     */
    public function standards(): HasMany
    {
        return $this->hasMany(SnarsStandard::class, 'chapter_id');
    }

    /**
     * Get the user who created the chapter.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the chapter.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active chapters.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the 'order' field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Set the group_id attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setGroupIdAttribute($value)
    {
        $this->attributes['group_id'] = $value;
        $this->attributes['snars_group_id'] = $value;
    }

    /**
     * Get the group_id attribute.
     *
     * @return string
     */
    public function getGroupIdAttribute()
    {
        return $this->attributes['snars_group_id'] ?? $this->attributes['group_id'] ?? null;
    }
}
