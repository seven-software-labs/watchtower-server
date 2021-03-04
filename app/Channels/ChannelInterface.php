<?php

namespace App\Channels;

use App\Models\Message;
use App\Http\Requests\CHannel\CreateChannelRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket;

interface ChannelInterface {
    /**
     * Get the channel name.
     */
    public function getChannelName();

    /**
     * Install the channel.
     */
    public function install();

    /**
     * Uninstall the channel.
     */
    public function uninstall();

    /**
     * Activate the channel.
     */
    public function activate();

    /**
     * Deactivate the channel.
     */
    public function deactivate();

    /**
     * Sync Channel
     * 
     * This function should be for syncing tickets between
     * the channel and Watchtower.
     */
    public function syncChannel();

    /**
     * Creates a new ticket.
     * 
     * This should create a new ticket for Watchtower.
     */
    public function createTicket(CreateTicketRequest $request);

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function sendMessage(Message $message);
}