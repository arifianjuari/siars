<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleActivationLog extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id',
        'tenant_id',
        'request_id',
        'user_id',
        'action',
        'notes',
        'old_data',
        'new_data',
        'action_at',
    ];

    /**
     * Atribut yang harus dikonversi
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'action_at' => 'datetime',
    ];

    /**
     * Mendapatkan modul terkait
     *
     * @return BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Mendapatkan tenant terkait
     *
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Mendapatkan permintaan aktivasi terkait
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(ModuleActivationRequest::class, 'request_id');
    }

    /**
     * Mendapatkan pengguna yang melakukan aksi
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk mendapatkan log berdasarkan jenis aksi
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk mendapatkan log berdasarkan modul
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $moduleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    /**
     * Scope untuk mendapatkan log berdasarkan tenant
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $tenantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope untuk mendapatkan log berdasarkan periode waktu
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('action_at', [$startDate, $endDate]);
    }

    /**
     * Mendapatkan deskripsi aksi yang mudah dibaca
     *
     * @return string
     */
    public function getActionTextAttribute(): string
    {
        $actions = [
            'activated' => 'Aktivasi Modul',
            'deactivated' => 'Deaktivasi Modul',
            'requested' => 'Permintaan Aktivasi',
            'approved' => 'Persetujuan Aktivasi',
            'rejected' => 'Penolakan Aktivasi',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }
}
