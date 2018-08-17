<?php

namespace Anacreation\CmsEmail\Listeners;


use Anacreation\CmsEmail\Events\EmailRegistration;
use App\Events\Event;
use Illuminate\Support\Facades\Log;

class EmailRegistrationHandler
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

        if ($event->list->confirm_opt_in) {
            Log::info("going to launch email confirmation email!");
        } elseif ($event->list->has_welcome_message) {
            Log::info("going to send welcome message");
        }

    }
}
