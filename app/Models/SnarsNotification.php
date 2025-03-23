<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SnarsNotification extends Model implements Auditable
{
    use HasFactory, HasUuids, \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snars_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'message',
        'data',
        'type',
        'status',
        'processed_at',
        'tenant_id',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'meta' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the notification.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the recipients for this notification.
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class, 'notification_id');
    }

    /**
     * Get the user who created the notification.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the notification.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to filter by notification type.
     */
    public function scopeOfType($query, $notificationType)
    {
        return $query->where('notification_type', $notificationType);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter active notifications (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to filter expired notifications.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to filter scheduled notifications.
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    /**
     * Scope a query to filter notifications ready to be sent.
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Menambahkan penerima notifikasi
     *
     * @param User|array|int $users
     * @return $this
     */
    public function addRecipients($users)
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            $userId = $user instanceof User ? $user->id : $user;

            $this->recipients()->updateOrCreate(
                ['user_id' => $userId],
                ['is_read' => false, 'read_at' => null]
            );
        }

        return $this;
    }

    /**
     * Menandai notifikasi sebagai diproses
     *
     * @return $this
     */
    public function markAsProcessing()
    {
        $this->status = 'processing';
        $this->save();

        return $this;
    }

    /**
     * Menandai notifikasi sebagai terkirim
     *
     * @return $this
     */
    public function markAsSent()
    {
        $this->status = 'sent';
        $this->processed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Menandai notifikasi sebagai gagal
     *
     * @return $this
     */
    public function markAsFailed()
    {
        $this->status = 'failed';
        $this->processed_at = now();
        $this->save();

        return $this;
    }
}
