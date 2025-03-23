<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRecipient extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
    ];

    /**
     * Atribut yang harus dikonversi
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Mendapatkan notifikasi yang terkait
     *
     * @return BelongsTo
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(SnarsNotification::class, 'notification_id');
    }

    /**
     * Mendapatkan pengguna yang terkait
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menandai notifikasi telah dibaca
     *
     * @return $this
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }

        return $this;
    }

    /**
     * Menandai notifikasi belum dibaca
     *
     * @return $this
     */
    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->is_read = false;
            $this->read_at = null;
            $this->save();
        }

        return $this;
    }
}
