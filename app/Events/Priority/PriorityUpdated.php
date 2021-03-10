<?php

namespace App\Events\Priority;

use App\Models\Priority;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriorityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The priority that is firing with the event.
     * 
     * @var \App\Models\Priority
     */
    public $priority;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Priority $priority)
    {
        $this->priority = $priority;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->priority->organization_id}-priority-{$this->priority->getKey()}-channel"),
            new PrivateChannel("organization-{$this->priority->organization_id}-priority-channel"),
        ];
    }
}
