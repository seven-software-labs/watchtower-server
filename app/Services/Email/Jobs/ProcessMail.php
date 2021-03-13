<?php

namespace App\Services\Email\Jobs;

use App\Models\Channel;
use App\Models\Message;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProcessMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The channel organization to be synced.
     */
    public $channel;

    /**
     * The mailbox to be synced.
     */
    public $mailbox;

    /**
     * The specific mail id to sync.
     */
    public $mailId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Channel $channel, \PhpImap\Mailbox $mailbox, int $mailId)
    {
        $this->channel = $channel;
        $this->mailbox = $mailbox;
        $this->mailId = $mailId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Lets pull the mail from the mailbox.
        $mail = $this->mailbox->getMail($this->mailId, false);

        $message_id = $mail->headers->message_id;
        $in_reply_to = $mail->headers->in_reply_to ?? null;

        // Check if there's a message with the mail id.
        $hasMessage = Message::where('source_id', $message_id)->exists();

        // Lets check if there's an "in_reply_to" and find a message for it.
        if(!blank($in_reply_to)) {
            $parentMessage = Message::select('ticket_id', 'source_id')
                ->where('source_id', $in_reply_to)
                ->first();
        } else {
            $parentMessage = null;
        }

        // If the ticket exists, we can ignore this mail.
        if ($hasMessage) {
            logger()->info("Mail ID {$this->mailId} already has a message.");
            return;
        } else {
            logger()->info("Creating message for Mail ID {$this->mailId}");
        }

        DB::beginTransaction();

        try {
            // Lets find or create the user that this ticket is going to belong to.
            $user = User::firstOrCreate([
                'email' => $mail->fromAddress,
            ], [
                'organization_id' => $this->channel->organization_id,
                'name' => $mail->fromName ?? $mail->fromAddress,
                'password' => Hash::make(Str::random(40)),
            ]);

            $user->channels()->syncWithoutDetaching($this->channel->getKey());

            // Lets create the ticket and the message.
            if($parentMessage) {
                $ticket = $parentMessage->ticket;
            } else {
                $ticket = Ticket::updateOrCreate([
                    'subject' => $mail->subject,
                    'user_id' => $user->getKey(),
                    'organization_id' => $this->channel->organization_id,
                ], [
                    'ticket_type_id' => TicketType::TICKET,
                    'department_id' => $this->channel->department_id,
                    'status_id' => $this->channel->organization->default_status->getKey(),
                    'priority_id' => $this->channel->organization->default_priority->getKey(),
                    'channel_id' => $this->channel->getKey(),
                ]);

                $ticket->messages()->create([
                    'content' => $mail->textHtml,
                    'message_type_id' => 1,
                    'user_id' => $user->getKey(),
                    'source_id' => $message_id,
                    'source_created_at' => Carbon::parse($mail->headers->date)->format('Y-m-d H:i:s'),
                ]);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            logger()->error($e);
            DB::rollback();
        }

        $this->mailbox->disconnect();
    }
}
