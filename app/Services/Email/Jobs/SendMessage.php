<?php

namespace App\Services\Email\Jobs;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
     * Create new mail.
     * 
     * @var PHPMailer\PHPMailer\PHPMailer
     */
    public function getMail()
    {
        // Get the channel settings.
        $settings = $this->channel->settings;

        logger()->info('settings');
        logger()->info($settings);

        // Instantiate the mail class.
        $mail = new PHPMailer();

        // Configure send settings.
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com'; // @todo Replace This
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $settings->get('email');               //SMTP username
        $mail->Password   = $settings->get('password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587; // @todo Replace This
        
        return $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the mail class.
        $mail = $this->getMail();
        
        // Get the message.
        $message = $this->message;

        try {
            //Recipients
            $mail->setFrom($message->user->email, $message->user->name);
            $mail->addAddress($message->ticket->user->email, $message->ticket->user->name);
        
            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $message->subject ?? "Re: ".$message->ticket->subject;
            $mail->Body    = $message->content;
            $mail->AltBody = $message->content;

            foreach($message->ticket->messages()->where('is_sent', true) as $ticketMessage) {
                $mail->addCustomHeader('In-Reply-To', $ticketMessage->source_id);
                $mail->addCustomHeader('References', $ticketMessage->source_id);
            }

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
}
