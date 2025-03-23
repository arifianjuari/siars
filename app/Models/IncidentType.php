<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
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
     * Get the incidents for this incident type.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Get the subtypes for this incident type.
     */
    public function subtypes()
    {
        return $this->hasMany(IncidentSubtype::class);
    }
}
