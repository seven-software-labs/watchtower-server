<?php

namespace App\Services\Twitter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Twitter\Jobs\ProcessMessages;

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
        // ...
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = \App\Models\Service::where('slug', 'twitter')->first();

        $organizations = \App\Models\Organization::with('channels')
            ->whereHas('channels', function($query) use($service) {
                $query->where('channels.is_active', true);
                $query->where('channels.service_id', $service->getKey());
            })->get();

        $organizations->each(function($organization) use($service) {
            $organization->channels()->where('service_id', $service->getKey())->get()->each(function($channel) {
                logger()->info('Channel: ' . $channel->name);
                ProcessMessages::dispatch($channel);
            });
        });
    }
}
