<?php

namespace App\Channels\Modules;

use App\Channels\ChannelInterface;
use App\Models\Channel;
use App\Models\User;
use App\Models\TicketType;
use App\Models\Message;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => true,
            'class' => get_class($this),
        ]);

        return;
    }

    /**
     * Uninstall the channel.
     */
    public function uninstall() {
        Channel::where('name', $this->getChannelName())
            ->where('slug', $this->getChannelSlug())
            ->delete();
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
     * Sync Channel
     * 
     * This function should be for syncing tickets between
     * the channel and Watchtower.
     */
    public function syncChannel() {
        $mailbox = new \PhpImap\Mailbox(
            '{pop.gmail.com:995/pop3/ssl}INBOX', // IMAP server and mailbox folder
            'yamato.takato@gmail.com', // Username for the before configured mailbox
            '', // Password for the before configured username
            __DIR__, // Directory, where attachments will be saved (optional)
            'UTF-8' // Server encoding (optional)
        );

        // set some connection arguments (if appropriate)
        // $mailbox->setConnectionArgs(OP_READONLY);

        try {
            // Get all emails (messages)
            // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
            $mailIds = $mailbox->searchMailbox('ALL');
        } catch(\PhpImap\Exceptions\ConnectionException $ex) {
            echo "IMAP connection failed: " . $ex;
            die();
        }

        // If $mailsIds is empty, no emails could be found
        if(!$mailIds) {
            return;
        }

        // Put the latest email on top of listing
        rsort($mailIds);

        // Get the last 15 emails only
        array_splice($mailIds, 15);

        // Loop through the emails.
        foreach($mailIds as $mailId)
        {
            // Check if there's a message with the mail id.
            $hasMessage = Message::where('source_id', $mailId)->exists();

            // If the ticket exists, we can ignore this mail.
            if ($hasMessage) {
                echo "Mail ID $mailId already has a message. \n";
                continue;
            }

            // Lets pull the mail from the mailbox.
            $mail = $mailbox->getMail($mailId, false);

            // Lets find or create the user that this ticket is going to belong to.
            $user = User::firstOrCreate([
                'email' => $mail->fromAddress,
            ], [
                'name' => $mail->fromName ?? $mail->fromAddress,
                'password' => Hash::make(Str::random(40)),
            ]);

            // Lets create the ticket and the message.
            $ticket = Ticket::updateOrCreate([
                'subject' => $mail->subject,
                'user_id' => $user->getKey(),
            ], [
                'ticket_type_id' => TicketType::TICKET,
                'department_id' => 1, // Customer Success
                'status_id' => 1, // Open
                'priority_id' => 2, // Medium
            ]);

            $ticket->messages()->create([
                'content' => $mail->textHtml,
                'message_type_id' => 1,
                'user_id' => $user->getKey(),
                'source_id' => $mailId,
            ]);
        }
    }

    /**
     * Creates a new ticket.
     * 
     * This should create a new ticket for Watchtower.
     */
    public function createTicket(CreateTicketRequest $request) {

    }

    /**
     * Creates a message for a ticket.
     * 
     * This should create a new message for a ticket.
     */
    public function createMessage(Ticket $ticket, CreateMessageRequest $request) {

    }
}