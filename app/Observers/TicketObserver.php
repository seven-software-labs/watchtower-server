<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Events\Ticket\TicketCreated;
use App\Events\Ticket\TicketUpdated;
use App\Events\Ticket\TicketDeleted;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        TicketCreated::dispatch($ticket);
    }

    /**
     * Handle the Ticket "updated" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        TicketUpdated::dispatch($ticket);
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        TicketDeleted::dispatch($ticket);
    }
}
