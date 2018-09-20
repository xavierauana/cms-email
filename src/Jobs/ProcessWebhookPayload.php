<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\CampaignStatus;
use Anacreation\CmsEmail\Models\EmailActivity;
use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessWebhookPayload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var array
     */
    protected $payload;


    /**
     * Create a new job instance.
     *
     * @param array $payload
     */
    public function __construct(Collection $payload) {
        //

        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        Log::info("ProcessWebhookPayload: Going to update webhook. Total count : {$this->payload->count()}");

        $count = 0;

        $this->payload->each(function ($item) use (&$count) {

            if (in_array($item['event'], array_keys(CampaignStatus::Status))) {
                $this->updateCampaignStatus($item);
            } elseif (in_array($item['event'],
                array_keys(EmailActivity::Activities))) {
                $this->updateEmailActivities($item);
            } else {
                Log::info("ProcessWebhookPayload: Not Tracked event : {$item['event']}");
            }

            $count++;
        });

        Log::info("ProcessWebhookPayload: Job finished, total count:{$count}");

    }

    private function updateCampaignStatus(array $item) {

        if (!isset($item['campaign_id']) or !isset($item['recipient_id'])) {
            return;
        }

        $status = $this->findCampaignStatus($item);

        $this->updateOrCreateCampaignStatus($item, $status);

    }

    private function updateEmailActivities($item) {

        if (!isset($item['campaign_id']) or !isset($item['recipient_id'])) {
            return;
        }


        EmailActivity::create([
            "campaign_id"  => $item['campaign_id'],
            "recipient_id" => $item['recipient_id'],
            'activity'     => $item['event'],
            'ip'           => $item['ip'] ?? null,
            'user_agent'   => $item['useragent'] ?? null,
            'url'          => $item['url'] ?? null,
            'timestamp'    => $item['timestamp'],
            'message_id'   => $item['sg_message_id']
        ]);

        if ($item['event'] === EmailActivity::Activities['spamreport']) {
            Recipient::whereId()->update([
                'status' => Recipient::StatusTypes['unsubscribed']
            ]);
        }
    }

    /**
     * @param array $item
     * @return mixed
     */
    private function findCampaignStatus(array $item) {

        $status = CampaignStatus::where([
            ["campaign_id", "=", $item['campaign_id']],
            ["recipient_id", "=", $item['recipient_id']],
            ["status", "=", CampaignStatus::Status["to_provider"]],
            ["message_id", "=", null],
        ])->orWhere([
            ["campaign_id", "=", $item['campaign_id']],
            ["recipient_id", "=", $item['recipient_id']],
            ["message_id", "=", $item['sg_message_id']],
        ])->first();

        return $status;
    }

    /**
     * @param array $item
     * @param       $status
     */
    private function updateOrCreateCampaignStatus(
        array $item, CampaignStatus $status = null
    ): void {
        if ($status) {
            $status->update([
                'status'     => $item['event'],
                'reason'     => $item['reason'] ?? null,
                "message_id" => $item['sg_message_id'] ?? null,
            ]);
        } else {
            CampaignStatus::create([
                'status'       => $item['event'],
                'reason'       => $item['reason'] ?? null,
                "message_id"   => $item['sg_message_id'],
                "campaign_id"  => $item['campaign_id'],
                "recipient_id" => $item['recipient_id'],
            ]);
        }
    }
}
