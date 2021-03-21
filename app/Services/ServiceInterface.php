<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;

interface ServiceInterface {
    /**
     * Get the channel name.
     */
    public function getServiceName();

    /**
     * Get the channel name.
     */
    public function getServiceSlug();

    /**
     * Install the channel.
     */
    public function install(): void;

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function sendMessage(Channel $channel, Message $message): bool;

    /**
     * Receives a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function receiveMessage(mixed $data): Message;

    /**
     * Get the additional actions for the service.
     */
    public function getActions(Channel $channel): array;
}