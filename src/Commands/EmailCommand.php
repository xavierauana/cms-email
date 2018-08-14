<?php

namespace Anacreation\CmsEmail\Commands;

use Anacreation\CmsEmail\Models\Campaign;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms_email_campaign:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled email campaign';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        Log::info("Cms Email scheduler method has run");

        Campaign::whereNotNull('schedule')
                ->whereHasSent(false)
                ->get()->each(function (Campaign $campaign) {
                $timezone = new DateTimeZone(config('app.timezone'));
                $now = Carbon::now($timezone);
                Log::info('Now: ' . $now->toTimeString());
                $newNow = $now->addMinutes(config('cms_email.scheduler_time_offset_minutes',
                    0));
                Log::info('Offset: ' . config('scheduler_time_offset_minutes',
                        0));
                Log::info('New now: ' . $newNow->toTimeString());
                Log::info('Schedule: ' . $campaign->schedule);
                $schedule = new Carbon($campaign->schedule);
                Log::info('Schedule Carbon: ' . $schedule);
                Log::info('New now and schedule class: ' . get_class($schedule) . " " . get_class($newNow));
                Log::info('Is now bigger: ' . $newNow->gt($schedule) ? "Yes" : "No");
                Log::info('Campaign Title: ' . $campaign->title);

                if ($newNow->gt($schedule)) {
                    $campaign->launch();
                    Log::info('Campaign send in : ' . Carbon::now()
                                                            ->toDateTimeString());
                }
            });
    }
}
