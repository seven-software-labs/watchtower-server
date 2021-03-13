<?php

namespace App\Events\Message;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message that is firing with the event.
     * 
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        // Get the ticket for the message.
        $ticket = $message->ticket;

        // Update the message's ticket to reflect the last_reply column appropriately.
        $ticket->update([
            'last_replied_at' => $message->source_created_at,
        ]);

        // Set the data.
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->message->ticket->organization_id}-ticket-{$this->message->ticket->getKey()}-channel"),
            new PrivateChannel("organization-{$this->message->ticket->organization_id}-ticket-channel"),
        ];
    }
}
