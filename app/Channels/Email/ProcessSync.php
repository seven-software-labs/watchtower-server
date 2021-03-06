<?php

namespace App\Channels\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Channels\Email\ProcessMailbox;

class ProcessSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $channel = \App\Models\Channel::where('slug', 'email')
            ->firstOrFail();

        $channelOrganizations = \App\Models\ChannelOrganization::query()
            ->where('channel_id', $channel->getKey())
            ->where('is_active', true)
            ->get();

        $channelOrganizations->each(function($channelOrganization) {
            ProcessMailbox::dispatch($channelOrganization);
        });
    }
}
