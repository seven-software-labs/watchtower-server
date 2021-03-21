<?php

namespace App\Observers;

use App\Models\Channel;
use App\Events\Channel\ChannelCreated;
use App\Events\Channel\ChannelUpdated;
use App\Events\Channel\ChannelDeleted;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function created(Channel $channel)
    {
        ChannelCreated::dispatch($channel);
    }

    /**
     * Handle the Channel "updated" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function updated(Channel $channel)
    {
        ChannelUpdated::dispatch($channel);
    }

    /**
     * Handle the Channel "deleted" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function deleted(Channel $channel)
    {
        ChannelDeleted::dispatch($channel);
    }
}
