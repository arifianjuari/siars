<?php

namespace App\Notifications;

use App\Models\ModuleActivationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModuleActivationRequested extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Objek permintaan aktivasi modul
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
        $requester = $this->request->requestedBy;

        return (new MailMessage)
            ->subject("Permintaan Aktivasi Modul dari {$tenant->name}")
            ->greeting("Halo!")
            ->line("Ada permintaan aktivasi modul baru dari {$tenant->name}.")
            ->line("Modul: {$module->name}")
            ->line("Diminta oleh: {$requester->name}")
            ->line("Catatan: {$this->request->notes}")
            ->action('Lihat Permintaan', url(route('module-activation.show', $this->request->id)))
            ->line('Silakan proses permintaan ini segera.');
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

        return [
            'id' => $this->request->id,
            'title' => "Permintaan Aktivasi Modul Baru",
            'message' => "Rumah Sakit {$tenant->name} meminta aktivasi modul {$module->name}",
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'module_id' => $module->id,
            'module_name' => $module->name,
            'requested_by' => $this->request->requested_by,
            'requested_by_name' => optional($this->request->requestedBy)->name,
            'requested_at' => $this->request->requested_at,
            'url' => route('module-activation.show', $this->request->id),
            'type' => 'module_activation_request',
        ];
    }
}
