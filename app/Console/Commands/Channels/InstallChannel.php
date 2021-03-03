<?php

namespace App\Console\Commands\Channels;

use App\Channels\Kernel as ChannelKernel;
use Illuminate\Console\Command;

class InstallChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channels:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a channel into Watchtower.';

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
        // Lets get all the available channel classes in app/Channels/Modules.
        $availableChannelClasses = collect(ChannelKernel::getModules());

        $channels = $availableChannelClasses->map(function($availableChannelClass) {
            return new $availableChannelClass;
        });

        // Lets prompt the user for input on which class to install.
        $selectedChannel = $this->choice(
            'Which channel do you want to install?',
            array_values($channels->map(function($channel) {
                return get_class($channel);
            })->toArray()),
        );

        // Lets instantiate the channel class for the selected channel.
        $channel = new $selectedChannel;
        $channel->install();

        $this->info("The {$channel->getChannelName()} channel was installed.");

        return 0;
    }
}
