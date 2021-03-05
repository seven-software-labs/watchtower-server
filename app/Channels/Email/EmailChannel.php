<?php

namespace App\Channels\Email;

use App\Channels\ChannelInterface;
use App\Channels\Email\SyncEmailChannelJob;
use App\Models\Channel;
use App\Models\ChannelSetting;
use App\Models\User;
use App\Models\TicketType;
use App\Models\Message;
use App\Models\Organization;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use DB;

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
        $channel = Channel::updateOrCreate([
            'name' => $this->getChannelName(),
            'slug' => $this->getChannelSlug(),
        ],[
            'is_active' => true,
            'class' => get_class($this),
        ]);

        $channelSettings = collect([
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
        // Get channel.
        $channel = Channel::where('name', $this->getChannelName())
            ->where('slug', $this->getChannelSlug())
            ->first();

        // Get channel settings.
        $channelSettings = ChannelSetting::where('channel_id', $channel->getKey())
            ->get();

        // Remove pivot entries
        // DB::table('channel_organization_settings')
        //     ->whereIn('channel_setting_id', $channelSettings->pluck('id'))
        //     ->delete();

        // Remove channel settings.
        $channelSettings->delete();
        // Remove channel.
        $channel->delete();
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
        $channel = Channel::where('slug', $this->getChannelSlug())
            ->firstOrFail();

        $channelOrganizations = \App\Models\ChannelOrganization::query()
            ->where('channel_id', $channel->getKey())
            ->where('is_active', true)
            ->get();

        $channelOrganizations->each(function($channelOrganization) {
            $this->syncInbox($channelOrganization);
        });
    }

    /**
     * Sync the inbox folder in the mailbox.
     */
    public function syncInbox(\App\Models\ChannelOrganization $channelOrganization): void
    {
        $mailbox = $this->getMailbox($channelOrganization);
        $settings = $channelOrganization->settings;

        try {
            $since = Carbon::now()->startOfDay()->format('d F Y H:i:s');
            $mailIds = $mailbox->searchMailbox('TO "'.$settings->get('email').'" SINCE "'.$since.'" UNDELETED');
            if(!$mailIds) return;
            rsort($mailIds);
        } catch(\PhpImap\Exceptions\ConnectionException $ex) {
            logger()->error("IMAP connection failed: " . $ex);
            return;
        }

        // Loop through the emails.
        foreach($mailIds as $mailId)
        {
            // Lets pull the mail from the mailbox.
            $mail = $mailbox->getMail($mailId, false);

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
                logger()->info("Mail ID $mailId already has a message.");
                continue;
            } else {
                logger()->info("Creating message for Mail ID $mailId");
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
                    'organization_id' => $channelOrganization->organization_id,
                ], [
                    'ticket_type_id' => TicketType::TICKET,
                    'department_id' => $channelOrganization->department_id,
                    'status_id' => 1, // Open
                    'priority_id' => 2, // Medium
                    'channel_id' => $channelOrganization->channel_id,
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
     * Create the mailbox.
     */
    public function getMailbox(\App\Models\ChannelOrganization $channelOrganization, $folder = 'INBOX'): \PhpImap\Mailbox
    {
        // Get the settings collection.
        $settings = $channelOrganization->settings;

        // Build the server string.
        $server = $settings->get('server');
        $port = $settings->get('port');
        $mode = $settings->get('mode');
        $encryption = $settings->get('encryption');

        // Build the mailbox.
        $mailbox = new \PhpImap\Mailbox(
            "{{$server}:{$port}/{$mode}/{$encryption}}$folder", 
            $settings->get('email'), 
            $settings->get('password'),
            false,
            'US-ASCII',
        );

        // Configure connection arguments.
        $mailbox->setConnectionArgs(OP_READONLY);

        return $mailbox;
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
            $mail->Password   = 'ULN922mx105';                               //SMTP password
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