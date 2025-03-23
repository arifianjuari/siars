<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_number',
        'title',
        'description',
        'reporter_id',
        'department_id',
        'incident_date',
        'incident_time',
        'category_id',
        'status',
        'immediate_action',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'incident_date' => 'date',
        'incident_time' => 'datetime:H:i',
    ];

    /**
     * Get the reporter that owns the report.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the department that owns the report.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the category that owns the report.
     */
    public function category()
    {
        return $this->belongsTo(RiskCategory::class, 'category_id');
    }

    /**
     * Get the factors for the report.
     */
    public function factors()
    {
        return $this->hasMany(RiskFactor::class, 'report_id');
    }

    /**
     * Get the mitigations for the report.
     */
    public function mitigations()
    {
        return $this->hasMany(RiskMitigation::class, 'report_id');
    }
}
