<?php

namespace App\Channels\Email;

use App\Channels\ChannelInterface;
use App\Channels\Email\ProcessSync;
use App\Channels\Email\SendMessage;
use App\Models\Channel;
use App\Models\ChannelSetting;
use App\Models\ChannelOrganization;
use App\Models\Message;
use Illuminate\Support\Str;

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
        $channel = Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => true,
            'class' => get_class($this),
        ]);

        collect([
            [
                'name' => 'server',
                'description' => '',
                'placeholder' => 'imap.gmail.com',
                'field_type' => 'text',
            ],
            [
                'name' => 'port',
                'description' => '',
                'placeholder' => '993',
                'field_type' => 'text',
            ],
            [
                'name' => 'mode',
                'description' => '',
                'placeholder' => 'imap',
                'field_type' => 'text',
            ],
            [
                'name' => 'encryption',
                'description' => '',
                'placeholder' => 'ssl',
                'field_type' => 'text',
            ],
            [
                'name' => 'email',
                'description' => '',
                'placeholder' => 'support@watchtowerapp.com',
                'field_type' => 'email'
            ],
            [
                'name' => 'password',
                'description' => '',
                'placeholder' => 'password',
                'field_type' => 'password',
            ],
        ])->each(function($channelSetting) use($channel) {
            ChannelSetting::updateOrCreate(array_merge($channelSetting, [
                'slug' => Str::slug($channelSetting['name']),
                'channel_id' => $channel->getKey(),
            ]));
        });

        return;
    }

    /**
     * Uninstall the channel.
     */
    public function uninstall() {
        // ...
    }

    /**
     * Activate the channel.
     */
    public function activate() {
        Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => true,
        ]);

        return;
    }

    /**
     * Deactivate the channel.
     */
    public function deactivate() {
        Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => false,
        ]);

        return;
    }

    /**
     * Sync Channel.
     * 
     * This function is for syncing tickets between
     * the channel and the application.
     */
    public function syncChannel(): void
    {
        ProcessSync::dispatch();
    }

    /**
     * Send a message through the channel.
     */
    public function sendMessage(ChannelOrganization $channelOrganization, Message $message): void {
        SendMessage::dispatchSync($channelOrganization, $message);
    }
}