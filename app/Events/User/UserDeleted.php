<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that is firing with the event.
     * 
     * @var \App\Models\User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->user->masterOrganization->getKey()}-user-{$this->user->getKey()}-channel"),
            new PrivateChannel("organization-{$this->user->masterOrganization->getKey()}-user-channel"),
        ];
    }
}
