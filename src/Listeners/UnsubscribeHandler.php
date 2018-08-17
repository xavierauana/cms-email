<?php

namespace Anacreation\CmsEmail\Listeners;


use Anacreation\CmsEmail\Events\EmailRegistration;
use App\Events\Event;
use Illuminate\Support\Facades\Log;

class UnsubscribeHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(EmailRegistration $event) {

        if ($event->list->has_goodbye_message) {
            Log::info("going to send goodbye message");
        }

    }
}
