<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\CampaignStatus;
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
     * @var \Anacreation\CmsEmail\Models\Campaign
     */
    public $campaign;
    /**
     * @var \Anacreation\CmsEmail\Models\Recipient
     */
    public $recipient;
    /**
     * @var bool
     */
    private $skip;

    /**
     * Create a new job instance.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign  $campaign
     * @param \Anacreation\CmsEmail\Models\Recipient $recipient
     * @param bool                                   $skip
     */
    public function __construct(
        Campaign $campaign, Recipient $recipient, bool $skip = true
    ) {
        //
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->skip = $skip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        Log::info("Job handle email for campaign id: {$this->campaign->id}, recipient id {$this->recipient->id}, email: {$this->recipient->email}");

        $status = $this->getCampaignStatus();

        if ($this->needToTerminate($status)) {
            return;
        }

        $htmlContent = $this->constructEmailContent();

        $emailProvider = $this->getEmailProvider();

        $mail = $emailProvider
            ->from($this->campaign->from_name, $this->campaign->from_address)
            ->to($this->recipient->name ?? "", $this->recipient->email)
            ->subject($this->campaign->subject)
            ->htmlContent($htmlContent);

        if (config("cms_email.sand_box")) {
            $this->randomFail();
            $mail = $mail->enableSendBox();
        }

        $response = $mail->send([
            'campaign_id'  => $this->campaign->id,
            'recipient_id' => $this->recipient->id,
        ]);

        if ($response->isOkay()) {

            $this->updateCampaignStatus($status);

            Log::info("SendEmailJob: Send to provider successfully. campaign id: {$this->campaign->id} , recipient id: $this->recipient->id");

        } else {

            Log::error("SendEmailJob: Post to api error, campaign id: {$this->campaign->id} , recipient id:{$this->recipient->id}",
                (array)$response->getResponse());

            throw new \Exception("SendEmailJob: Post to api error, campaign id: {$this->campaign->id} , recipient id: {$this->recipient->id}");

        }

    }

    /**
     * @return string
     * @throws \Throwable
     */
    private function constructEmailContent(): string {
        $htmlContent = view(config('cms_email.template_folder') . "/" . $this->campaign->template)
            ->with([
                'name'      => $this->recipient->name,
                'campaign'  => $this->campaign,
                'recipient' => $this->recipient,
                'user'      => $this->recipient->user,
            ])->render();

        return $htmlContent;
    }

    /**
     * @return mixed
     */
    private function getEmailProvider(): EmailSender {
        $emailProvider = app()->makeWith(EmailSender::class,
            [
                'username' => config("cms_email.username"),
                'password' => config("cms_email.password"),
            ]);

        return $emailProvider;
    }

    private function randomFail() {
        $int = rand(1, 5);

        if ($int === 1) {
            throw new \Exception("ramdom fails");
        }
    }

    /**
     * @return mixed
     */
    private function getCampaignStatus() {
        $status = CampaignStatus::where([
            ['campaign_id', "=", $this->campaign->id],
            ['recipient_id', "=", $this->recipient->id],
        ])->first();

        if ($status === null) {
            $status = CampaignStatus::create([
                'campaign_id'  => $this->campaign->id,
                'recipient_id' => $this->recipient->id,
                'status'       => CampaignStatus::Status['none']
            ]);
        }

        return $status;
    }

    private function needToTerminate(CampaignStatus $status) {

        return $status->status !== CampaignStatus::Status['none'] and $this->skip;
    }

    /**
     * @param $status
     */
    private function updateCampaignStatus($status): void {
        $status->update([
            'campaign_id'  => $this->campaign->id,
            'recipient_id' => $this->recipient->id,
            'status'       => CampaignStatus::Status['to_provider']
        ]);
    }
}
