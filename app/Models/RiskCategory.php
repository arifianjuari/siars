<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskCategory extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable, SoftDeletes;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'risk_categories';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'parent_id',
        'order',
        'is_active',
        'metadata',
        'created_by',
        'updated_by',
    ];

    /**
     * Atribut yang harus dikonversi.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'metadata' => 'json',
    ];

    /**
     * Mendapatkan tenant yang memiliki kategori risiko.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Mendapatkan kategori induk.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(RiskCategory::class, 'parent_id');
    }

    /**
     * Mendapatkan subkategori.
     */
    public function children(): HasMany
    {
        return $this->hasMany(RiskCategory::class, 'parent_id');
    }

    /**
     * Mendapatkan risiko-risiko dalam kategori ini.
     */
    public function risks(): HasMany
    {
        return $this->hasMany(RiskRegistry::class, 'category_id');
    }

    /**
     * Mendapatkan risiko-risiko dalam subkategori ini.
     */
    public function subCategoryRisks(): HasMany
    {
        return $this->hasMany(RiskRegistry::class, 'subcategory_id');
    }

    /**
     * Mendapatkan user yang membuat.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mendapatkan user yang mengupdate.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk kategori utama (bukan subkategori).
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope untuk subkategori.
     */
    public function scopeSubCategories($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope untuk kategori aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
