<?php

namespace App\Channels\Email;

use App\Channels\ChannelInterface;
use App\Channels\Email\SyncEmailChannelJob;
use App\Models\Channel;
use App\Models\User;
use App\Models\TicketType;
use App\Models\Message;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
            '{pop.gmail.com:993/imap/ssl}', // IMAP server and mailbox folder
            'yamato.takato@gmail.com', // Username for the before configured mailbox
            '', // Password for the before configured username
            false, // Directory, where attachments will be saved (optional)
            'US-ASCII' // Server encoding (optional)
        );

        // set some connection arguments (if appropriate)
        // $mailbox->setConnectionArgs(OP_READONLY);

        try {
            // Get all emails (messages)
            // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
            $since = Carbon::now()->subHour()->format('d F Y H:i:s');
            $mailIds = $mailbox->searchMailbox('SINCE "'.$since.'"');
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
        array_splice($mailIds, 5);

        // Loop through the emails.
        foreach($mailIds as $mailId)
        {
            // Lets pull the mail from the mailbox.
            $mail = $mailbox->getMail($mailId, false);

            $message_id = $mail->headers->message_id;
            $in_reply_to = $mail->headers->in_reply_to ?? null;
            $references = $mail->headers->references ?? null;

            // Check if there's a message with the mail id.
            $hasMessage = Message::where('source_id', $message_id)->exists();
            $parentMessage = Message::select('ticket_id', 'source_id')
                ->where('source_id', $in_reply_to)
                ->orWhere('source_id', $references)
                ->first();

            // If the ticket exists, we can ignore this mail.
            if ($hasMessage) {
                echo "Mail ID $mailId already has a message. \n";
                continue;
            } else {
                echo "Creating message for Mail ID $mailId";
            }

            // Lets find or create the user that this ticket is going to belong to.
            $user = User::firstOrCreate([
                'email' => $mail->fromAddress,
            ], [
                'name' => $mail->fromName ?? $mail->fromAddress,
                'password' => Hash::make(Str::random(40)),
            ]);

            // Lets create the ticket and the message.
            if($parentMessage) {
                $ticket = $parentMessage->ticket;
            } else {
                $ticket = Ticket::updateOrCreate([
                    'subject' => $mail->subject,
                    'user_id' => $user->getKey(),
                ], [
                    'ticket_type_id' => TicketType::TICKET,
                    'department_id' => 1, // Customer Success
                    'status_id' => 1, // Open
                    'priority_id' => 2, // Medium
                    'channel_id' => Channel::where('slug', $this->getChannelSlug())->firstOrFail()->getKey(),
                ]);
            }

            $ticket->messages()->create([
                'content' => $mail->textHtml,
                'message_type_id' => 1,
                'user_id' => $user->getKey(),
                'source_id' => $message_id,
                'source_created_at' => Carbon::parse($mail->headers->date)->format('Y-m-d H:i:s'),
            ]);
        }

        // Disconnect from mailbox
        $mailbox->disconnect();
    }

    /**
     * Send a message through the channel.
     */
    public function sendMessage(Message $message) {
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'yamato.takato@gmail.com';                     //SMTP username
            $mail->Password   = '';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom($message->user->email, $message->user->name);
            $mail->addAddress($message->ticket->user->email, $message->ticket->user->name);
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $message->subject ?? "Re: ".$message->ticket->subject;
            $mail->Body    = $message->content;
            $mail->AltBody = $message->content;

            if($mail->send()) {
                $message->update([
                    'source_id' => $mail->getLastMessageID(),
                    'is_sent' => true,
                ]);
            }
            
            return $message;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
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