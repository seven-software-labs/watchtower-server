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
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Host       = $settings->get('smtp_email_server');
        $mail->Port       = $settings->get('smtp_port');
        $mail->Username   = $settings->get('email');
        $mail->Password   = $settings->get('password');
        
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

        // References
        $references = Message::where('ticket_id', $this->message->ticket_id)
            ->latest()
            ->pluck('source_id');

        // Last Message
        $lastMessage = Message::where('ticket_id', $this->message->ticket_id)
            ->latest()
            ->first();

        try {
            //Recipients
            $mail->setFrom($message->user->email, $message->user->name);
            $mail->addAddress($message->ticket->user->email, $message->ticket->user->name);
        
            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $message->subject ?? "Re: ".$message->ticket->subject;
            $mail->Body    = $message->content;
            $mail->AltBody = $message->content;

            if ($lastMessage->getKey() != $message->getKey()) {
                $mail->addCustomHeader('In-Reply-To', $lastMessage->source_id);
            }

            if(count($references) > 1) {
                foreach($references as $reference) {
                    if($reference != "") {
                        $mail->addCustomHeader('References', $reference);
                    }
                }
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
