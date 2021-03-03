<?php

namespace App\Channels;

trait ChannelTrait {
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
}