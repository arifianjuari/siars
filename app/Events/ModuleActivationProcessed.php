<?php

namespace App\Events;

use App\Models\ModuleActivationRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleActivationProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Objek permintaan aktivasi modul yang telah diproses
     * 
     * @var ModuleActivationRequest
     */
    public $request;

    /**
     * Create a new event instance.
     * 
     * @param ModuleActivationRequest $request
     */
    public function __construct(ModuleActivationRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tenant.' . $this->request->tenant_id),
        ];
    }
}
