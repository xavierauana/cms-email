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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AssignToSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Anacreation\CmsEmail\Models\Campaign
     */
    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     */
    public function __construct(Campaign $campaign) {
        //
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $this->campaign->list
            ->recipients()
            ->whereIn('status', $this->campaign->to_status)
            ->chunk(1000, function (Collection $recipients) {
                $recipients->each(function (
                    Recipient $recipient
                ) {
                    $queue = config("cms_email.send_email_queue",
                        "default");

                    Log::info("Dispatch job for {$recipient->email} and campaign id: {$this->campaign->id}");

                    $job = new SendEmail($this->campaign, $recipient);

                    dispatch($job)->onQueue($queue);
                });
            });
    }
}
