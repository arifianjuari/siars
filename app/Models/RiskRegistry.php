<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class RiskRegistry extends Model
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'risk_registry';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'risk_code',
        'title',
        'description',
        'category_id',
        'subcategory_id',
        'location_id',
        'owner_id',
        'status',
        'date_identified',
        'reporter_id',
        'current_assessment_id',
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
        'date_identified' => 'datetime',
        'metadata' => 'json',
        'location_id' => 'integer',
    ];

    /**
     * Mendapatkan tenant yang memiliki risiko.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Mendapatkan kategori risiko.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RiskCategory::class, 'category_id');
    }

    /**
     * Mendapatkan subkategori risiko.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(RiskCategory::class, 'subcategory_id');
    }

    /**
     * Mendapatkan lokasi/departemen tempat terjadinya risiko.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'location_id');
    }

    /**
     * Mendapatkan pemilik risiko.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Mendapatkan pelapor risiko.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Mendapatkan penilaian risiko terbaru.
     */
    public function currentAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class, 'current_assessment_id');
    }

    /**
     * Mendapatkan semua penilaian risiko.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class, 'risk_id');
    }

    /**
     * Mendapatkan penilaian risiko inherent.
     */
    public function inherentAssessment(): HasOne
    {
        return $this->hasOne(RiskAssessment::class, 'risk_id')
            ->where('inherent_assessment', true)
            ->latest('assessment_date');
    }

    /**
     * Mendapatkan penilaian risiko residual.
     */
    public function residualAssessment(): HasOne
    {
        return $this->hasOne(RiskAssessment::class, 'risk_id')
            ->where('residual_assessment', true)
            ->latest('assessment_date');
    }

    /**
     * Mendapatkan rencana mitigasi.
     */
    public function mitigationPlans(): HasMany
    {
        return $this->hasMany(MitigationPlan::class, 'risk_id');
    }

    /**
     * Mendapatkan semua review risiko.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(RiskReview::class, 'risk_id');
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
     * Scope untuk risiko aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan status risiko.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope berdasarkan kategori risiko.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope untuk risiko dengan tingkat risiko tinggi.
     */
    public function scopeHighRisk($query)
    {
        return $query->whereHas('currentAssessment', function ($q) {
            $q->whereIn('risk_level', ['high', 'extreme']);
        });
    }
}
