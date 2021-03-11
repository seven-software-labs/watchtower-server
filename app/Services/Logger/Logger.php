<?php

namespace App\Services\Logger;

use App\Models\Message;
use App\Models\Service;
use App\Services\ServiceInterface;

class Logger implements ServiceInterface {
    /**
     * The service name.
     * 
     * @var string
     */
    public $serviceName = 'Logger';

    /**
     * The service slug.
     * 
     * @var string
     */
    public $serviceSlug = 'logger';

    /**
     * Get the service name.
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
    
    /**
     * Get the service name.
     */
    public function getServiceSlug()
    {
        return $this->serviceSlug;
    }

    /**
     * Install the service.
     */
    public function install(): void
    {
        Service::create([
            'name' => $this->getServiceName(),
            'slug' => $this->getServiceSlug(),
            'class' => get_class($this),
            'settings_schema' => collect([
                // ...
            ])->toJSON(),
        ]);
    }

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function sendMessage(Message $message): bool 
    {
        logger()->log('info', 'Sending Message', [
            'message' => $message,
        ]);

        $message->update([
            'is_sent' => true,
            'is_delivered' => true,
        ]);

        return true;
    }

    /**
     * Receives a message for a ticket.
     * 
     * @param Illuminate\Http\Request $request
     */
    public function receiveMessage($request): Message
    {
        return Message::create([
            // ...
        ]);
    }
}