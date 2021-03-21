<?php

namespace App\Services\Twitter\Jobs;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The channel to be synced.
     */
    public $channel;

    /**
     * The twitter message to be synced.
     */
    public $twitterMessage;

    /**
     * The twitter user to be synced.
     */
    public $twitterUser;

    /**
     * The twitter user to be synced.
     */
    public $twitterUserTarget;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Channel $channel, $twitterMessage, $twitterUser, $twitterUserTarget)
    {
        $this->channel = $channel;
        $this->twitterMessage = $twitterMessage;
        $this->twitterUser = $twitterUser;
        $this->twitterUserTarget = $twitterUserTarget;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger()->info("Processing Twitter Message", [
            'twitterMessage' => $this->twitterMessage,
        ]);

        // Check if there's a message with the twitter message id.
        $message = Message::where('source_id', $this->twitterMessage->id)->first();

        // If the twitter message exists, we can ignore.
        if ($message) {
            logger()->info("Twitter Message ID {$this->twitterMessage->id} already has a message.");
            return;
        } else {
            logger()->info("Creating message for Twitter Message ID {$this->twitterMessage->id}.");
        }

        DB::beginTransaction();

        try {
            // Lets find or create the user that this ticket is going to belong to.
            Model::unguard();

            $user = User::updateOrCreate([
                'master_organization_id' => $this->channel->organization_id,
                'twitter_user_id' => $this->twitterUser->id,
            ], [
                'name' => $this->twitterUser->name,
                'password' => Hash::make(Str::random(40)),
            ]);

            $targetUser = User::updateOrCreate([
                'master_organization_id' => $this->channel->organization_id,
                'twitter_user_id' => $this->twitterUserTarget->id
            ], [
                'name' => $this->twitterUserTarget->name,
                'password' => Hash::make(Str::random(40)),
            ]);

            Model::reguard();

            logger()->info("Found User", ['user' => $user]);
            logger()->info("Found Target", ['target' => $targetUser]);

            $user->channels()->syncWithoutDetaching($this->channel->getKey());

            // Find a message where the channel is Twitter and user or target is from Twitter.
            $channel = $this->channel;
            $localTwitterMessage = Message::whereHas('ticket', function($query) use($channel) {
                    $query->where('tickets.channel_id', $channel->getKey());
                })
                ->where(function($query) use ($user, $targetUser) {
                    $query->where(function($subQuery) use ($user, $targetUser) {
                        $subQuery->where('messages.user_id', $user->getKey());
                        $subQuery->where('messages.target_user_id', $targetUser->getKey());
                    });

                    $query->orWhere(function($subQuery) use ($user, $targetUser) {
                        $subQuery->where('messages.user_id', $targetUser->getKey());
                        $subQuery->where('messages.target_user_id', $user->getKey());
                    });
                })->first();

            logger()->info('localTwitterMessage', [
                'localTwitterMessage' => $localTwitterMessage,
            ]);

            $twitterTicketForUser = null;

            if($localTwitterMessage) {
                $twitterTicketForUser = $localTwitterMessage->ticket;
            }

            // Lets find or create the ticket and the message.
            if($twitterTicketForUser) {
                $ticket = $twitterTicketForUser;  

                logger()->info("Found Twitter Ticket", ['ticket' => $ticket]);
            } else {
                $ticketUser = $user->getKey();

                if($targetUser->is_customer) {
                    $ticketUser = $targetUser->getKey();
                }

                $ticket = Ticket::create([
                    'subject' => substr($this->twitterMessage->message_create->message_data->text, 0, 50),
                    'user_id' => $ticketUser,
                    'organization_id' => $this->channel->organization_id,
                    'ticket_type_id' => TicketType::TICKET,
                    'department_id' => $this->channel->department_id,
                    'status_id' => $this->channel->organization->default_status->getKey(),
                    'priority_id' => $this->channel->organization->default_priority->getKey(),
                    'channel_id' => $this->channel->getKey(),
                ]);

                logger()->info("Created Ticket", ['ticket' => $ticket]);
            }

            $message = $ticket->messages()->create([
                'content' => $this->twitterMessage->message_create->message_data->text,
                'message_type_id' => 1,
                'user_id' => $user->getKey(),
                'target_user_id' => $targetUser->getKey(),
                'source_id' => $this->twitterMessage->id,
                'source_created_at' => Carbon::createFromTimestamp($this->twitterMessage->created_timestamp / 1000)->format('Y-m-d H:i:s'),
            ]);

            logger()->info("New Message", ['message' => $message]);
            
            DB::commit();
        } catch (\Exception $e) {
            logger()->error($e);
            DB::rollback();
        }
    }
}