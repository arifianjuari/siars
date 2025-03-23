<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class MitigationPlan extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'mitigation_plans';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'risk_id',
        'title',
        'description',
        'strategy',
        'target_date',
        'owner_id',
        'status',
        'effectiveness_rating',
        'effectiveness_notes',
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
        'target_date' => 'date',
        'effectiveness_rating' => 'integer',
        'metadata' => 'json',
    ];

    /**
     * Mendapatkan risiko yang terkait dengan rencana mitigasi.
     */
    public function risk(): BelongsTo
    {
        return $this->belongsTo(RiskRegistry::class, 'risk_id');
    }

    /**
     * Mendapatkan pemilik rencana mitigasi.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Mendapatkan tindakan mitigasi.
     */
    public function actions(): HasMany
    {
        return $this->hasMany(MitigationAction::class, 'plan_id');
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
     * Scope untuk rencana mitigasi yang direncanakan.
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
     * Scope untuk rencana mitigasi yang sedang berlangsung.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope untuk rencana mitigasi yang sudah selesai.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk rencana mitigasi yang terlambat.
     */
    public function scopeOverdue($query)
    {
        return $query->whereDate('target_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Menghitung persentase penyelesaian tindakan.
     */
    public function getCompletionPercentageAttribute()
    {
        $actions = $this->actions;

        if ($actions->isEmpty()) {
            return 0;
        }

        $completedCount = $actions->where('status', 'completed')->count();
        return ($completedCount / $actions->count()) * 100;
    }
}
