<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class RiskReview extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'risk_reviews';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'risk_id',
        'review_date',
        'reviewer_id',
        'comments',
        'status',
        'next_review_date',
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
        'review_date' => 'datetime',
        'next_review_date' => 'date',
        'metadata' => 'json',
    ];

    /**
     * Mendapatkan risiko yang ditinjau.
     */
    public function risk(): BelongsTo
    {
        return $this->belongsTo(RiskRegistry::class, 'risk_id');
    }

    /**
     * Mendapatkan peninjau.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
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
     * Scope untuk review yang pending.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk review yang sudah selesai.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk review yang jatuh tempo dalam waktu tertentu.
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereDate('next_review_date', '>=', now())
            ->whereDate('next_review_date', '<=', now()->addDays($days))
            ->where('status', 'pending');
    }

    /**
     * Scope untuk review yang terlambat.
     */
    public function scopeOverdue($query)
    {
        return $query->whereDate('next_review_date', '<', now())
            ->where('status', 'pending');
    }
}
