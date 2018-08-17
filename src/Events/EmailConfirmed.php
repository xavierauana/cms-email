<?php

namespace Anacreation\CmsEmail\Events;

use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var \Anacreation\CmsEmail\Models\EmailList
     */
    public $list;
    /**
     * @var \Anacreation\CmsEmail\Models\Recipient
     */
    public $recipient;

    /**
     * Create a new event instance.
     *
     * @param \Anacreation\CmsEmail\Models\EmailList $list
     * @param \Anacreation\CmsEmail\Models\Recipient $recipient
     */
    public function __construct(EmailList $list, Recipient $recipient) {
        //
        $this->list = $list;
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
}
