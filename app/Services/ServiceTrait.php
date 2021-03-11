<?php

namespace App\Services;

use App\Models\ChannelOrganization;
use App\Models\Message;

trait ServiceTrait {
    /**
     * Get the channel's corresponding channel module.
     */
    public function getChannelModuleAttribute()
    {
        return new $this->class;
    }

    /**
     * Sync the channel's messages with Watchtower tickets.
     */
    public function syncChannel()
    {
        $this->channelModule->syncChannel();
    }

    /**
     * Send a message through the channel.
     */
    public function sendMessage(ChannelOrganization $channelOrganization, Message $message)
    {
        return $this->channelModule->sendMessage($channelOrganization, $message);
    }
}