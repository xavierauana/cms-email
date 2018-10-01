<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Entities\CampaignDTO;
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
    use Dispatchable, InteractsWithQueue, Queueable;

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

        $itemPerPage = $this->getItemPerChunk($query);

        $query->chunk($itemPerPage,
            function (Collection $recipients) use (&$count) {

                dispatch(new AssignToSender($this->campaign, $recipients));

                Log::info("PrepareJobs: batch {$count}");

                $count++;

            });

    }

    /**
     * @param $total
     * @return int
     */
    private function getNumberOfJobs($total): int {

        switch ($total) {
            case $total > 5000:
                return 100;
            case $total > 1000:
                return 10;
            default:
                return 5;
        }
    }

    /**
     * @param $query
     * @return float
     */
    private function getItemPerChunk($query): float {

        $total = $query->count();

        $numberOfJobs = $this->getNumberOfJobs($total);

        // show balance between timeout and memory
        $itemPerPage = ceil($total / $numberOfJobs);

        return $itemPerPage;
    }
}
