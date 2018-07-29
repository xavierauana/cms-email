<?php

namespace Anacreation\CmsEmail\Mail;

use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    /**
     * @var Campaign
     */
    private $campaign;
    /**
     * @var \Anacreation\CmsEmail\Models\Recipient
     */
    private $recipient;

    /**
     * Create a new message instance.
     *
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign, Recipient $recipient) {
        //
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->to($this->recipient->email, $this->recipient->name)
                    ->from($this->campaign->from_address,
                        $this->campaign->from_name)
                    ->replyTo($this->campaign->reply_address)
                    ->subject($this->campaign->subject)
                    ->view(config('cms_email.template_folder') . "." . $this->campaign->template)
                    ->with([
                        'user'     => $this->recipient->user,
                        'name'     => $this->recipient->name,
                        'campaign' => $this->campaign
                    ]);
    }
}
