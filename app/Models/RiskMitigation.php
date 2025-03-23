<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMitigation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'action_description',
        'responsible_id',
        'target_date',
        'status',
        'completion_date',
        'effectiveness',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_date' => 'date',
        'completion_date' => 'date',
    ];

    /**
     * Get the risk report that owns the mitigation.
     */
    public function report()
    {
        return $this->belongsTo(RiskReport::class, 'report_id');
    }

    /**
     * Get the user responsible for the mitigation.
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
