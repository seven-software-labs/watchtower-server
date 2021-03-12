<?php

namespace App\Services\Email;

use App\Models\Message;
use App\Models\Service;
use App\Services\Email\Jobs\ProcessSync;
use App\Services\ServiceInterface;

class Email implements ServiceInterface {
    /**
     * The service name.
     * 
     * @var string
     */
    public $serviceName = 'Email';

    /**
     * The service slug.
     * 
     * @var string
     */
    public $serviceSlug = 'email';

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
        Service::updateOrCreate([
            'name' => $this->getServiceName(),
            'slug' => $this->getServiceSlug(),
        ], [
            'class' => get_class($this),
            'settings_schema' => collect([
                [
                    'label' => 'Email Server',
                    'name' => 'email_server',
                    'description' => 'The email server. (e.g. imap.gmail.com)',
                    'placeholder' => 'The email server. (e.g. imap.gmail.com)',
                    'field_type' => 'text',
                ],
                [
                    'label' => 'Email Server Port',
                    'name' => 'port',
                    'description' => 'The port for the email server. (e.g. 993 for imap.gmail.com)',
                    'placeholder' => 'The port for the email server. (e.g. 993 for imap.gmail.com)',
                    'field_type' => 'text',
                ],
                [
                    'label' => 'Service',
                    'name' => 'service',
                    'description' => 'The email service to use.',
                    'placeholder' => 'The email service to use.',
                    'field_type' => 'select',
                    'options' => [
                        [
                            'label' => 'IMAP',
                            'value' => 'imap',
                        ],
                        [
                            'label' => 'POP3',
                            'value' => 'pop3',
                        ],
                    ],
                ],
                [
                    'label' => 'Email',
                    'name' => 'email',
                    'description' => 'The email to be used.',
                    'placeholder' => 'The email to be used.',
                    'field_type' => 'email',
                ],
                [
                    'label' => 'Password',
                    'name' => 'password',
                    'description' => 'The password for the email.',
                    'placeholder' => 'The password for the email.',
                    'field_type' => 'password',
                ],
            ])->toJSON(),
        ]);
    }

    /**
     * Sync the messages from the service and the application.
     */
    public function syncService(): void
    {
        logger()->info('Syncing Email Service');
        
        ProcessSync::dispatch();
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