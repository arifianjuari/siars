<?php

namespace App\Listeners;

use App\Events\ModuleActivationProcessed;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendModuleActivationProcessedNotification implements ShouldQueue
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
    public function handle(ModuleActivationProcessed $event): void
    {
        $this->notificationService->sendModuleActivationProcessedNotification($event->request);
    }
}
