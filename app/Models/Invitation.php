<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Invitation extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'organizer_department_id',
        'meeting_title',
        'meeting_datetime',
        'meeting_location',
        'meeting_agenda',
        'signatory_position',
        'signatory_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meeting_datetime' => 'datetime',
    ];

    /**
     * Get the document that owns the invitation.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the organizer department for the invitation.
     */
    public function organizerDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'organizer_department_id');
    }

    /**
     * Get the meeting minutes for the invitation.
     */
    public function meetingMinutes(): HasOne
    {
        return $this->hasOne(MeetingMinute::class);
    }
}
