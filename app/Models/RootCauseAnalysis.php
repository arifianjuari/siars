<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RootCauseAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'incident_id',
        'team_factors',
        'system_factors',
        'patient_factors',
        'environmental_factors',
        'analysis_method',
        'root_causes',
        'recommendations',
        'analyzed_by',
        'analyzed_at',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'analyzed_at' => 'datetime',
    ];

    /**
     * Get the incident that owns the analysis.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the user who analyzed the incident.
     */
    public function analyzedBy()
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }

    /**
     * Get the user who last updated the analysis.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
