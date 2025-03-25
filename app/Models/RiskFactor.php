<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskFactor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'tenant_id',
        'report_id',
        'factor_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the risk report that owns the factor.
     */
    public function report()
    {
        return $this->belongsTo(RiskReport::class, 'report_id');
    }

    /**
     * Scope untuk memfilter data yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
