<?php

namespace App\Listeners;

use App\Events\ModuleActivationRequested;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendModuleActivationRequestedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Layanan notifikasi
     * 
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(ModuleActivationRequested $event): void
    {
        $this->notificationService->sendModuleActivationRequestedNotification($event->request);
    }
}
