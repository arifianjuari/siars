<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskClassification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'incident_id',
        'dampak',
        'dampak_detail',
        'probabilitas',
        'probabilitas_detail',
        'skor_risiko',
        'level_risiko',
        'zona_risiko',
        'classifier_id',
    ];

    /**
     * Get the incident that owns the risk classification.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the user who classified the risk.
     */
    public function classifier()
    {
        return $this->belongsTo(User::class, 'classifier_id');
    }
}
