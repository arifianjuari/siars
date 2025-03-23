<?php

namespace App\Notifications;

use App\Models\ModuleActivationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModuleActivationProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Objek permintaan aktivasi modul yang diproses
     * 
     * @var ModuleActivationRequest
     */
    protected $request;

    /**
     * Create a new notification instance.
     * 
     * @param ModuleActivationRequest $request
     */
    public function __construct(ModuleActivationRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tenant = $this->request->tenant;
        $module = $this->request->module;
        $processor = $this->request->processedBy;

        $subject = $this->request->status === 'approved'
            ? "Permintaan Aktivasi Modul Disetujui"
            : "Permintaan Aktivasi Modul Ditolak";

        $message = $this->request->status === 'approved'
            ? "Permintaan aktivasi modul {$module->name} telah disetujui."
            : "Permintaan aktivasi modul {$module->name} telah ditolak.";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Halo!")
            ->line($message);

        return $mailMessage
            ->line("Diproses oleh: {$processor->name}")
            ->line("Catatan: {$this->request->notes}")
            ->action('Lihat Detail', url(route('module-activation.show', $this->request->id)));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $tenant = $this->request->tenant;
        $module = $this->request->module;
        $status = $this->request->status;

        return [
            'id' => $this->request->id,
            'title' => $status === 'approved' ? "Permintaan Aktivasi Modul Disetujui" : "Permintaan Aktivasi Modul Ditolak",
            'message' => $status === 'approved'
                ? "Permintaan aktivasi modul {$module->name} untuk {$tenant->name} telah disetujui"
                : "Permintaan aktivasi modul {$module->name} untuk {$tenant->name} telah ditolak",
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'module_id' => $module->id,
            'module_name' => $module->name,
            'status' => $status,
            'processed_by' => $this->request->processed_by,
            'processed_by_name' => optional($this->request->processedBy)->name,
            'processed_at' => $this->request->processed_at,
            'url' => route('module-activation.show', $this->request->id),
            'type' => 'module_activation_processed',
        ];
    }
}
