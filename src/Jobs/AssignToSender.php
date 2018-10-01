<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Entities\CampaignDTO;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AssignToSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var \Anacreation\CmsEmail\Models\Campaign
     */
    protected $campaign;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $recipients;

    /**
     * Create a new job instance.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @param \Illuminate\Support\Collection        $recipients
     */
    public function __construct(Campaign $campaign, Collection $recipients) {
        //
        $this->campaign = $campaign;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $this->recipients->each(function (Recipient $recipient) {

            Log::info("Dispatch job for {$recipient->email} and campaign id: {$this->campaign->id}");

            $queue = config("cms_email.send_email_queue", "default");

            dispatch(new SendEmail($this->campaign,
                $recipient))->onQueue($queue);
        });
    }
}
