<?php

namespace App\Events\Message;

use App\Models\Message;
use App\Models\Ticket;
use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The ticket class that is firing with the event.
     */
    private Ticket $ticket;

    /**
     * The message resource that is firing with the event.
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
        $this->ticket = $message->ticket;

        // Update the message's ticket to reflect the last_reply column appropriately.
        $this->ticket->update([
            'last_replied_at' => $message->source_created_at,
        ]);

        // Set the data.
        $this->message = new MessageResource($message);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->ticket->organization_id}-ticket-{$this->ticket->getKey()}-channel"),
            new PrivateChannel("organization-{$this->ticket->organization_id}-ticket-channel"),
        ];
    }
}
