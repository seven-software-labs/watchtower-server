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

use App\Channels\Email\ProcessSync;
use Illuminate\Support\Facades\Redis;

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