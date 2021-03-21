<?php

namespace App\Services\Email\Jobs;

use App\Models\Channel;
use App\Services\Email\Jobs\ProcessMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ProcessMailbox implements ShouldQueue
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
     * Creates a new \PhpImap\Mailbox instance.
     */
    public function getMailbox(): \PhpImap\Mailbox
    {
        // Get the settings collection.
        $settings = $this->channel->settings;

        // Build the server string.
        $server = $settings->get('imap_email_server');
        $port = $settings->get('imap_port');

        // Build the mailbox.
        $mailbox = new \PhpImap\Mailbox(
            "{{$server}:{$port}/imap/ssl}INBOX",
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the settings collection.
        $settings = $this->channel->settings;

        // Generate the mailbox.
        $mailbox = $this->getMailbox();

        try {
            $since = Carbon::now()->startOfDay()->format('d F Y H:i:s');
            $mailIds = $mailbox->searchMailbox('TO "'.$settings->get('email').'" SINCE "'.$since.'" UNDELETED');
            if(!$mailIds) return;
            rsort($mailIds);
        } catch(\PhpImap\Exceptions\ConnectionException $ex) {
            logger()->error("IMAP Connection Failed: " . $ex);
            return;
        }
        
        foreach($mailIds as $mailId) {
            ProcessMail::dispatch($this->channel, $mailbox, $mailId);
        }
    }
}
