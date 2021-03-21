<?php

namespace App\Services\Email;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Service;
use App\Services\Email\Jobs\ProcessSync;
use App\Services\Email\Jobs\SendMessage;
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
                    'label' => 'IMAP Email Server',
                    'name' => 'imap_email_server',
                    'description' => 'The email server used to receive emails. (e.g. imap.gmail.com)',
                    'placeholder' => 'The email server used to receive emails. (e.g. imap.gmail.com)',
                    'field_type' => 'text',
                ],
                [
                    'label' => 'IMAP Email Server Port',
                    'name' => 'imap_port',
                    'description' => 'The port for the email server used to receive emails. (e.g. 993 for imap.gmail.com)',
                    'placeholder' => 'The port for the email server used to receive emails. (e.g. 993 for imap.gmail.com)',
                    'field_type' => 'text',
                ],
                [
                    'label' => 'SMTP Email Server',
                    'name' => 'smtp_email_server',
                    'description' => 'The email server used to send emails. (e.g. smtp.gmail.com)',
                    'placeholder' => 'The email server used to send emails. (e.g. smtp.gmail.com)',
                    'field_type' => 'text',
                ],
                [
                    'label' => 'SMTP Email Server Port',
                    'name' => 'smtp_port',
                    'description' => 'TLS/STARTTLS Port (e.g. 587 for smtp.gmail.com)',
                    'placeholder' => 'TLS/STARTTLS Port (e.g. 587 for smtp.gmail.com)',
                    'field_type' => 'text',
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
    public function sendMessage(Channel $channel, Message $message): bool 
    {
        SendMessage::dispatchSync($channel, $message);
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

    /**
     * Get the additional actions for the service.
     */
    public function getActions(Channel $channel): array
    {
        return [];
    }    
}