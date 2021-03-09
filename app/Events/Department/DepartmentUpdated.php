<?php

namespace App\Events\Department;

use App\Models\Department;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The department that is firing with the event.
     * 
     * @var \App\Models\Department
     */
    public $department;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("organization-{$this->department->organization_id}-department-{$this->department->getKey()}-channel"),
            new PrivateChannel("organization-{$this->department->organization_id}-department-channel"),
        ];
    }
}
