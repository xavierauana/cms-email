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
    public function __construct(Campaign $campaign, Recipient $recipient
    ) {
        //
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        Log::info("Job handle email for campaign id: {$this->campaign->id}, recipient id {$this->recipient->id}, email: {$this->recipient->email}");

        $htmlContent = $this->constructEmailContent();

        $emailProvider = $this->getEmailProvider();

        $emailProvider->from($this->campaign->from_name,
            $this->campaign->from_address)
                      ->to($this->recipient->name, $this->recipient->email)
                      ->subject($this->campaign->subject)
                      ->htmlContent($htmlContent)
                      ->send(['campaign_id' => $this->campaign->id]);

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
    private function getEmailProvider(): mixed {
        $emailProvider = app()->makeWith(EmailSender::class,
            [
                'username' => config("cms_email.username"),
                'password' => config("cms_email.password"),
            ]);

        return $emailProvider;
    }
}
