<?php

namespace Anacreation\CmsEmail\Listeners;


use Anacreation\CmsEmail\Events\EmailConfirmed;
use Illuminate\Support\Facades\Log;

class EmailConfirmedHandler
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
    public function handle(EmailConfirmed $event) {
        if ($event->list->has_welcome_message) {
            Log::info("going to send welcome message");
        }
    }
}
