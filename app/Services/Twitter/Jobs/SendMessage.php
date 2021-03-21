<?php

namespace App\Services\Twitter\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The channel that we're sending with.
     * 
     * @var \App|Models\Channel
     */
    public $channel;

    /**
     * The message we're sending
     * 
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Channel $channel, Message $message)
    {
        $this->channel = $channel->withoutRelations();
        $this->message = $message;
    }  

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the settings collection.
        $settings = $this->channel->settings;

        $CONSUMER_KEY = "Sc7cPoAwOaEvksBgnxJUKII0f";
        $CONSUMER_SECRET = "ZbyYzzjEnTkfFyYlNj9VdNkmSr40dckv9nEcCND9nSQQF9cRsa";
        $ACCESS_TOKEN = $settings->get('access_token');
        $ACCESS_TOKEN_SECRET = $settings->get('access_token_secret');

        $connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

        $payload = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $this->message->ticket->user->twitter_user_id,
                    ],
                    'message_data' => [
                        'text' => $this->message->content,
                    ],
                ],
            ],
        ];

        logger()->info('payload', [
            'payload' => $payload,
        ]);

        $twitterMessage = $connection->post("direct_messages/events/new", $payload, true);

        logger()->info('message', [
            'message' => $twitterMessage,
        ]);

        // Update the DB Message.
        $this->message->update([
            'source_id' => $twitterMessage->event->id,
            'is_sent' => true,
        ]);
    }
}
