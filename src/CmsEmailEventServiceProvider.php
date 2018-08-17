<?php

namespace Anacreation\CmsEmail;

use Anacreation\CmsEmail\Events\EmailConfirmed;
use Anacreation\CmsEmail\Events\EmailRegistration;
use Anacreation\CmsEmail\Events\Unsubscribe;
use Anacreation\CmsEmail\Listeners\EmailConfirmedHandler;
use Anacreation\CmsEmail\Listeners\EmailRegistrationHandler;
use Anacreation\CmsEmail\Listeners\UnsubscribeHandler;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;

class CmsEmailEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EmailRegistration::class => [
            EmailRegistrationHandler::class
        ],
        Unsubscribe::class       => [
            UnsubscribeHandler::class
        ],
        EmailConfirmed::class    => [
            EmailConfirmedHandler::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        $check = config('cms_email.enable_workflow', "true") === true;

        if ($check) {
            parent::boot();
        }
    }

}
