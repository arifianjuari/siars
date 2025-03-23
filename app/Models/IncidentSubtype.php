<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentSubtype extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'incident_type_id',
        'name',
        'description',
        'is_active',
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
     * Get the incident type that owns this subtype.
     */
    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }

    /**
     * Get the incidents for this subtype.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
