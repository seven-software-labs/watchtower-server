<?php

namespace App\Services\Twitter;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Service;
use App\Services\Twitter\Jobs\ProcessSync;
use App\Services\Twitter\Jobs\SendMessage;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Twitter implements ServiceInterface {
    /**
     * The service name.
     * 
     * @var string
     */
    public $serviceName = 'Twitter';

    /**
     * The service slug.
     * 
     * @var string
     */
    public $serviceSlug = 'twitter';

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
                    'label' => 'Access Token',
                    'name' => 'access_token',
                    'description' => '[Authorize your account after channel creation.]',
                    'placeholder' => '[Authorize your account after channel creation.]',
                    'readonly' => true,
                    'field_type' => 'password',
                ],
                [
                    'label' => 'Access Token Secret',
                    'name' => 'access_token_secret',
                    'description' => '[Authorize your account after channel creation.]',
                    'placeholder' => '[Authorize your account after channel creation.]',
                    'readonly' => true,
                    'field_type' => 'password',
                ],
            ])->toJSON(),
        ]);
        
        if(!Schema::hasColumn('users', 'twitter_user_id'))
        {
            Schema::table('users', function (Blueprint $table) {
                $table->string('twitter_user_id')->nullable();
            });
        }
    }

    /**
     * Sync the messages from the service and the application.
     */
    public function syncService(): void
    {
        logger()->info('Syncing Twitter Service');

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
        return new Message;
    }

    /**
     * Get the additional actions for the service.
     */
    public function getActions(Channel $channel): array
    {
        return [
            [
                'label' => 'Authorize Account',
                'path' => route('services.twitter.channels.authorize-account', ['channel_id' => $channel->getKey()]),
            ],
        ];
    }
}