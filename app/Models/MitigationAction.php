<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class MitigationAction extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'mitigation_actions';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'title',
        'description',
        'due_date',
        'assignee_id',
        'status',
        'completion_date',
        'completion_notes',
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
        'due_date' => 'date',
        'completion_date' => 'date',
        'metadata' => 'json',
    ];

    /**
     * Mendapatkan rencana mitigasi.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(MitigationPlan::class, 'plan_id');
    }

    /**
     * Mendapatkan penanggung jawab tindakan mitigasi.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
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
     * Mendapatkan risiko terkait.
     */
    public function risk()
    {
        return $this->plan->risk;
    }

    /**
     * Scope untuk tindakan yang belum dimulai.
     */
    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not_started');
    }

    /**
     * Scope untuk tindakan yang sedang berlangsung.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope untuk tindakan yang sudah selesai.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk tindakan yang dibatalkan.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope untuk tindakan yang terlambat.
     */
    public function scopeOverdue($query)
    {
        return $query->whereDate('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope untuk tindakan yang jatuh tempo dalam waktu tertentu.
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->whereNotIn('status', ['completed', 'cancelled']);
    }
}
