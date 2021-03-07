<?php

namespace App\Channels;

use App\Models\ChannelOrganization;
use App\Models\Message;
use App\Http\Requests\Ticket\CreateTicketRequest;

interface ChannelInterface {
    /**
     * Get the channel name.
     */
    public function getChannelName();
    /**
     * Get the channel name.
     */
    public function getChannelSlug();

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
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function sendMessage(ChannelOrganization $channelOrganization, Message $message);
}