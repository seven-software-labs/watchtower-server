<?php

namespace App\Console\Commands\Channels;

use App\Models\Channel;
use Illuminate\Console\Command;

class SyncChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channels:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the sync command for all active channels.';

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
        $activeChannels = Channel::where('is_active', true)->get();

        $this->withProgressBar($activeChannels, function ($activeChannel) {
            $class = new $activeChannel->class;
            $class->syncChannel();
        });

        echo "\n";

        return 0;
    }
}
