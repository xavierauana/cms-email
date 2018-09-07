<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\Recipient;
use Anacreation\Notification\Provider\Contracts\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \Anacreation\Notification\Provider\Contracts\EmailSender
     */
    public $emailSender;
    /**
     * @var \Anacreation\CmsEmail\Models\Campaign
     */
    public $campaign;
    /**
     * @var \Anacreation\CmsEmail\Models\Recipient
     */
    public $recipient;

    /**
     * Create a new job instance.
     *
     * @param \Anacreation\Notification\Provider\Contracts\EmailSender $emailSender
     * @param \Anacreation\CmsEmail\Models\Campaign                    $campaign
     * @param \Anacreation\CmsEmail\Models\Recipient                   $recipient
     */
    public function __construct(
        EmailSender $emailSender, Campaign $campaign, Recipient $recipient
    ) {
        //
        $this->emailSender = $emailSender;
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        Log::info("Job handle email for campaign id: {$this->campaign->id}");
        Log::info("Recipient id {$this->recipient->id}, email: {$this->recipient->email}");

        $this->emailSender->send(['campaign_id' => $this->campaign->id]);
    }
}
