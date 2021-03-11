<?php

namespace App\Events\Channel;

use App\Models\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The channel that is firing with the event.
     * 
     * @var \App\Models\Channel
     */
    public $channel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->channel->organization_id}-channel-{$this->channel->getKey()}-channel"),
            new PrivateChannel("organization-{$this->channel->organization_id}-channel-channel"),
        ];
    }
}
