<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class RiskAssessment extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'risk_assessments';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'risk_id',
        'likelihood_score',
        'impact_score',
        'risk_level',
        'assessment_date',
        'assessor_id',
        'rationale',
        'inherent_assessment',
        'residual_assessment',
        'target_assessment',
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
        'assessment_date' => 'datetime',
        'likelihood_score' => 'integer',
        'impact_score' => 'integer',
        'inherent_assessment' => 'boolean',
        'residual_assessment' => 'boolean',
        'target_assessment' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Mendapatkan risiko yang dinilai.
     */
    public function risk(): BelongsTo
    {
        return $this->belongsTo(RiskRegistry::class, 'risk_id');
    }

    /**
     * Mendapatkan user yang melakukan penilaian.
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
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
     * Risiko yang menggunakan penilaian ini sebagai penilaian terbaru.
     */
    public function currentForRisks(): HasMany
    {
        return $this->hasMany(RiskRegistry::class, 'current_assessment_id');
    }

    /**
     * Menghitung tingkat risiko berdasarkan likelihood dan impact.
     */
    public function calculateRiskLevel(): string
    {
        $score = $this->likelihood_score * $this->impact_score;

        if ($score >= 15) {
            return 'extreme';
        } elseif ($score >= 8) {
            return 'high';
        } elseif ($score >= 4) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Boot method untuk model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->risk_level) {
                $model->risk_level = $model->calculateRiskLevel();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['likelihood_score', 'impact_score'])) {
                $model->risk_level = $model->calculateRiskLevel();
            }
        });
    }

    /**
     * Scope untuk penilaian risiko inherent.
     */
    public function scopeInherent($query)
    {
        return $query->where('inherent_assessment', true);
    }

    /**
     * Scope untuk penilaian risiko residual.
     */
    public function scopeResidual($query)
    {
        return $query->where('residual_assessment', true);
    }

    /**
     * Scope untuk penilaian risiko target.
     */
    public function scopeTarget($query)
    {
        return $query->where('target_assessment', true);
    }

    /**
     * Scope untuk risiko tinggi.
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'extreme']);
    }
}
