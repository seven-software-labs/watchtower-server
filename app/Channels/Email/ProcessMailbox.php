<?php

namespace App\Channels\Email;

use App\Channels\Email\ProcessMail;
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
     * The channel organization to be synced.
     * 
     * @var \App\Models\ChannelOrganization;
     */
    public $channelOrganization;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\Models\ChannelOrganization $channelOrganization)
    {
        $this->channelOrganization = $channelOrganization;
    }

    /**
     * Creates a new \PhpImap\Mailbox instance.
     */
    public function getMailbox(): \PhpImap\Mailbox
    {
        // Get the settings collection.
        $settings = $this->channelOrganization->settings;

        // Build the server string.
        $server = $settings->get('server');
        $port = $settings->get('port');
        $mode = $settings->get('mode');
        $encryption = $settings->get('encryption');
        $folder = 'INBOX';

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the settings collection.
        $settings = $this->channelOrganization->settings;

        // Generate the mailbox.
        $mailbox = $this->getMailbox();

        try {
            $since = Carbon::now()->startOfDay()->format('d F Y H:i:s');
            $mailIds = $mailbox->searchMailbox('TO "'.$settings->get('email').'" SINCE "'.$since.'" UNDELETED');
            if(!$mailIds) return;
            rsort($mailIds);
        } catch(\PhpImap\Exceptions\ConnectionException $ex) {
            logger()->error("IMAP connection failed: " . $ex);
            return;
        }
        
        foreach($mailIds as $mailId) {
            ProcessMail::dispatch($this->channelOrganization, $mailbox, $mailId);
        }
    }
}
