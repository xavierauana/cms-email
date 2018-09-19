<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PrepareJobs implements ShouldQueue
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

        $count = 0;

        $query = $this->campaign
            ->list
            ->recipients()
            ->whereIn('status', $this->campaign->to_status);

        $total = $query->count();

        $numberOfJobs = 99;

        // show balance between timeout and memory
        $itemPerPage = ceil(100005 / $numberOfJobs);

        $query->chunk($itemPerPage,
            function (Collection $recipients) use (&$count) {

                $job = (new AssignToSender($this->campaign,
                    $recipients))->delay(1);

                dispatch($job);

                Log::info("PrepareJobs: batch {$count}");

                $count++;

            });

    }
}
