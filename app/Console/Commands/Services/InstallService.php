<?php

namespace App\Console\Commands\Services;

use App\Services\Kernel as ServiceKernel;
use Illuminate\Console\Command;

class InstallService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a service into Watchtower.';

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
        // Lets get all the available service classes in app/Services/Modules.
        $availableServiceClasses = collect(ServiceKernel::getModules());

        $services = $availableServiceClasses->map(function($availableServiceClass) {
            return new $availableServiceClass;
        });

        // Lets prompt the user for input on which class to install.
        $selectedService = $this->choice(
            'Which service do you want to install?',
            array_values($services->map(function($service) {
                return get_class($service);
            })->toArray()),
        );

        // Lets instantiate the service class for the selected service.
        $service = new $selectedService;
        $service->install();

        $this->info("The {$service->getServiceName()} service was installed.");

        return 0;
    }
}
