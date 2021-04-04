<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\Department;
use App\Models\Message;
use App\Models\Organization;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Observers\ChannelObserver;
use App\Observers\DepartmentObserver;
use App\Observers\MessageObserver;
use App\Observers\OrganizationObserver;
use App\Observers\PriorityObserver;
use App\Observers\StatusObserver;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Channel::observe(ChannelObserver::class);
        Department::observe(DepartmentObserver::class);
        Message::observe(MessageObserver::class);
        Organization::observe(OrganizationObserver::class);
        Priority::observe(PriorityObserver::class);
        Status::observe(StatusObserver::class);
        Ticket::observe(TicketObserver::class);
        User::observe(UserObserver::class);
    }
}
