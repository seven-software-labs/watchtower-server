<?php

namespace App\Channels\Modules;

use App\Channels\ChannelInterface;
use App\Models\Channel;
use Illuminate\Http\Request;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket;

class EmailChannel implements ChannelInterface {
    /**
     * The channel name.
     * 
     * @var string
     */
    public $channelName = 'Email';

    /**
     * The channel slug.
     * 
     * @var string
     */
    public $channelSlug = 'email';

    /**
     * Get the channel name.
     */
    public function getChannelName() {
        return $this->channelName;
    }

    /**
     * Get the channel slug.
     */
    public function getChannelSlug() {
        return $this->channelSlug;
    }

    /**
     * Install the channel.
     */
    public function install() {
        Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => true,
        ]);

        return;
    }

    /**
     * Uninstall the channel.
     */
    public function uninstall() {
        Channel::where('name', $this->getChannelName())
            ->where('slug', $this->getChannelSlug())
            ->delete();
    }

    /**
     * Activate the channel.
     */
    public function activate() {

    }

    /**
     * Deactivate the channel.
     */
    public function deactivate() {

    }

    /**
     * Sync Tickets
     * 
     * This function should be for syncing tickets between
     * the channel and Watchtower.
     */
    public function syncTickets() {
        // Get new emails from the SMTP server.
        // updateOrCreate the tickets found within Watchtower.
    }

    /**
     * Creates a new ticket.
     * 
     * This should create a new ticket for Watchtower.
     */
    public function createTicket(CreateTicketRequest $request) {

    }

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function createMessage(Ticket $ticket, CreateMessageRequest $request) {

    }
}