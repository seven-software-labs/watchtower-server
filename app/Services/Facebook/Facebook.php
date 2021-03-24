<?php

namespace App\Services\Facebook;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Service;
use App\Services\ServiceInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Facebook implements ServiceInterface {
    /**
     * The service name.
     * 
     * @var string
     */
    public $serviceName = 'Facebook';

    /**
     * The service slug.
     * 
     * @var string
     */
    public $serviceSlug = 'facebook';

    /**
     * Get the channel name.
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Get the channel name.
     */
    public function getServiceSlug()
    {
        return $this->serviceSlug;
    }

    /**
     * Install the channel.
     */
    public function install(): void
    {
        Service::updateOrCreate([
            'name' => $this->getServiceName(),
            'slug' => $this->getServiceSlug(),
        ], [
            'class' => get_class($this),
            'settings_schema' => collect([
                // ...
            ])->toJSON(),
            'required_fields' => collect([
                'facebook_user_id',
            ])->toJSON(),
        ]);

        if(!Schema::hasColumn('users', 'facebook_user_id'))
        {
            Schema::table('users', function (Blueprint $table) {
                $table->string('facebook_user_id')->nullable();
            });
        }
    }

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function sendMessage(Channel $channel, Message $message): bool
    {
        return true;
    }

    /**
     * Receives a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function receiveMessage(mixed $data): Message
    {
        return new Message;
    }

    /**
     * Get the additional actions for the service.
     */
    public function getActions(Channel $channel): array
    {
        return [
            // ...
        ];
    }
}