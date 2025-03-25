<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Memo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'sender_department_id',
        'recipient_department_id',
        'sender_name',
        'recipient_name',
        'reference_regulation',
        'place',
        'signatory_position',
        'signatory_name',
        'rank',
        'nrp',
        'carbon_copy',
    ];

    /**
     * Get the document that owns the memo.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the sender department for the memo.
     */
    public function senderDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sender_department_id');
    }

    /**
     * Get the recipient department for the memo.
     */
    public function recipientDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'recipient_department_id');
    }
}
