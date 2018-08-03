<?php

namespace Anacreation\CmsEmail;

use Anacreation\Cms\Models\Cms;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\CmsEmail;
use Anacreation\Notification\Provider\Contracts\EmailSender;
use Anacreation\Notification\Provider\ServiceProviders\SendGrid;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CmsEmailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->views();

        $this->config();

        $this->registerCmsPlugin();

        app()->bind(EmailSender::class, SendGrid::class);

        app()->booted(function () {
            $schedule = app()->make(Schedule::class);
            $schedule->call(function () {

                Log::info("Cms Email scheduler method has run");

                Campaign::whereNotNull('schedule')
                        ->whereHasSent(false)
                        ->get()->each(function (Campaign $campaign) {
                        $timezone = new DateTimeZone(config('app.timezone'));
                        $now = Carbon::now($timezone);
                        Log::info('Now: ' . $now->toTimeString());
                        $newNow = $now->addMinutes(config('scheduler_time_offset_minutes',
                            0));
                        Log::info('New now: ' . $newNow->toTimeString());
                        $schedule = $campaign->schedule;
                        Log::info('Schedule: ' . $schedule->toTimeString());
                        if ($newNow->gt($schedule)) {
                            $campaign->launch();
                        }
                    });
            })->everyMinute();
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom(
            __DIR__ . '/config/cms_email.php', 'cms_email'
        );
    }

    private function views() {
        $this->loadViewsFrom(__DIR__ . '/views', 'cms_email');

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/cms/email'),
        ], 'cms_email_views');

    }

    private function config() {
        $this->publishes([
            __DIR__ . '/config/cms_email.php' => config_path('cms_email.php'),
        ], 'cms_email_config');
    }

    private function registerCmsPlugin(): void {
        Cms::registerCmsPlugins('CmsEmail', 'Email', 'email/campaigns');
        Cms::registerCmsPluginRoutes('CmsEmail', function () {
            CmsEmail::routes();
        });
    }

}
