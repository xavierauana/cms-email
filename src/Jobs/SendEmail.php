<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\Notification\Provider\Contracts\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \Anacreation\Notification\Provider\Contracts\EmailSender
     */
    private $emailSender;
    /**
     * @var \Anacreation\CmsEmail\Models\Campaign
     */
    private $campaign;

    /**
     * Create a new job instance.
     *
     * @param \Anacreation\Notification\Provider\Contracts\EmailSender $emailSender
     * @param \Anacreation\CmsEmail\Models\Campaign                    $campaign
     */
    public function __construct(EmailSender $emailSender, Campaign $campaign) {
        //
        $this->emailSender = $emailSender;
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->emailSender->send(['campaign_id' => $this->campaign->id]);
    }
}
