<?php

namespace App\Services\Email\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Email\Jobs\ProcessMailbox;

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
        $service = \App\Models\Service::where('slug', 'email')->first();

        $organizations = \App\Models\Organization::with('channels')
            ->whereHas('channels', function($query) use($service) {
                $query->where('channels.is_active', true);
                $query->where('channels.service_id', $service->getKey());
            })->get();

        $organizations->each(function($organization) use($service) {
            $organizationChannels = $organization->channels()
                ->where('service_id', $service->getKey())
                ->where('is_active', true)
                ->get();

            $organizationChannels->each(function($channel) {
                logger()->info('Channel: ' . $channel->name);
                ProcessMailbox::dispatch($channel);
            });
        });
    }
}
