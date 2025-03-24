<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'case_number',
        'tanggal_waktu_kejadian',
        'location_id',
        'incident_type_id',
        'incident_subtype_id',
        'nama_pasien',
        'no_rm',
        'kronologis',
        'reporter_id',
        'status',
        'tanggal_evaluasi',
        'detail_penanganan',
        'hasil_monitoring',
        'status_penutupan',
        'catatan_tambahan',
        'dokumen_pendukung',
        'dokumen_evaluasi',
        'tindakan_langsung',
        'handler_id',
        'handling_date',
        'handling_actions',
        'handling_result',
        'handling_document',
        'follow_up_plan',
        'completed_at',
        'profesi_id',
        'pernah_terjadi_sebelumnya',
        'qr_code_path',
        'qr_code_base64',
        'nama_pelapor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_waktu_kejadian' => 'datetime',
        'tanggal_evaluasi' => 'datetime',
        'handling_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the location associated with the incident.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the incident type associated with the incident.
     */
    public function type()
    {
        return $this->belongsTo(IncidentType::class, 'incident_type_id');
    }

    /**
     * Get the incident type associated with the incident.
     */
    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }

    /**
     * Get the incident subtype associated with the incident.
     */
    public function subtype()
    {
        return $this->belongsTo(IncidentSubtype::class, 'incident_subtype_id');
    }

    /**
     * Get the incident subtype associated with the incident.
     */
    public function incidentSubtype()
    {
        return $this->belongsTo(IncidentSubtype::class);
    }

    /**
     * Get the reporter (user) who reported the incident.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the handler (user) who handled the incident.
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    /**
     * Get the risk classification record associated with the incident.
     */
    public function classification()
    {
        return $this->hasOne(RiskClassification::class);
    }

    /**
     * Get the root cause analysis record associated with the incident.
     */
    public function analysis()
    {
        return $this->hasOne(RootCauseAnalysis::class);
    }

    /**
     * Get the root cause analysis record associated with the incident.
     */
    public function rootCauseAnalysis()
    {
        return $this->hasOne(RootCauseAnalysis::class);
    }
}
