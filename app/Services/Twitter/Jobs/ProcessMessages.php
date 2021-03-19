<?php

namespace App\Services\Twitter\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Channel;
use App\Services\Twitter\Jobs\ProcessMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The channel to be synced.
     * 
     * @var \App\Models\Channel;
     */
    public $channel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Get the TwitterOAuth connection.
     */
    public function getConnection(): TwitterOAuth
    {
        // Get the settings collection.
        $settings = $this->channel->settings;

        $CONSUMER_KEY = "Sc7cPoAwOaEvksBgnxJUKII0f";
        $CONSUMER_SECRET = "ZbyYzzjEnTkfFyYlNj9VdNkmSr40dckv9nEcCND9nSQQF9cRsa";
        $ACCESS_TOKEN = $settings->get('access_token');
        $ACCESS_TOKEN_SECRET = $settings->get('access_token_secret');

        $connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

        return $connection;
    }

    /**
     * Gets the list of messages from the account.
     */
    public function getMessages(): object
    {
        return $this->getConnection()->get("direct_messages/events/list");
    }

    /**
     * Gets the user from the message.
     */
    public function getUser($twitter_user_id)
    {
        return $this->getConnection()->get("users/show", [
            'user_id' => $twitter_user_id,
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the messages.
        $messages = $this->getMessages();

        logger()->info("Messages", [
            'messages' => $messages,
        ]);

        if(isset($messages->events)) {
            foreach($messages->events as $message) {
                $twitterUser = $this->getUser($message->message_create->sender_id);
                $twitterUserTarget = $this->getUser($message->message_create->target->recipient_id);

                // logger()->info('twitterUser', [
                //     'twitterUser' => $twitterUser,
                //     'twitterUserTarget' => $twitterUserTarget,
                // ]);

                ProcessMessage::dispatch($this->channel, $message, $twitterUser, $twitterUserTarget);
            }
        }
    }
}
