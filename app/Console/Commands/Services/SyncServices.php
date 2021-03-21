<?php

namespace App\Console\Commands\Services;

use App\Models\Service;
use Illuminate\Console\Command;

class SyncServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the sync command for all active services.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeServices = Service::where('is_active', true)->get();

        $this->withProgressBar($activeServices, function ($activeService) {
            $activeService->syncService();
        });

        echo "\n";

        return 0;
    }
}
