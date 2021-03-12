<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;

trait ServiceTrait {
    /**
     * Get the service's corresponding service module.
     */
    public function getServiceModuleAttribute()
    {
        return new $this->class;
    }

    /**
     * Sync the service's messages with Watchtower tickets.
     */
    public function syncService()
    {
        $this->serviceModule->syncService();
    }

    /**
     * Send a message through the channel.
     */
    public function sendMessage(Channel $channel, Message $message)
    {
        return $this->serviceModule->sendMessage($channel, $message);
    }
}