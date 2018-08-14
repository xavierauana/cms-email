<?php
/**
 * Author: Xavier Au
 * Date: 14/4/2018
 * Time: 6:48 PM
 */

namespace Anacreation\CmsEmail\Models;


use Anacreation\CmsEmail\Controllers\CampaignContentsController;
use Anacreation\CmsEmail\Controllers\CampaignRecipientsController;
use Anacreation\CmsEmail\Controllers\CampaignsController;
use Anacreation\CmsEmail\Controllers\EmailListRecipientsController;
use Anacreation\CmsEmail\Controllers\EmailListsController;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class CmsEmail
{
    public static function routes(): void {


        Route::group([
            'middleware' => [
                'web'
            ]
        ],
            function () {
                Route::get('email/campaigns/{campaign}',
                    CampaignsController::class . "@show");

                Route::get('lists/{list}/recipients/unsubscribe',
                    EmailListRecipientsController::class . "@unsubscribe")
                     ->name('lists.unsubscribe');

                Route::group(['prefix' => config('admin.route_prefix')],
                    function () {
                        Route::group([
                            'middleware' => 'auth:admin',
                            'prefix'     => 'email'
                        ],
                            function () {


                                Route::post('campaigns/{campaign}/send',
                                    CampaignsController::class . "@send");
                                Route::post('campaigns/{campaign}/contents/update',
                                    CampaignContentsController::class . "@update");
                                Route::get('campaigns/{campaign}/contents',
                                    CampaignContentsController::class . "@index")
                                     ->name('campaigns.contents.index');
                                Route::resource('campaigns',
                                    CampaignsController::class);
                                Route::resource('campaigns.recipients',
                                    CampaignRecipientsController::class);

                                Route::resource('lists',
                                    EmailListsController::class);

                                Route::get('lists/{list}/recipients/import',
                                    EmailListRecipientsController::class . "@showImport")
                                     ->name('lists.recipients.import');
                                Route::post('lists/{list}/recipients/import',
                                    EmailListRecipientsController::class . "@import");

                                Route::resource('lists.recipients',
                                    EmailListRecipientsController::class);
                            });

                    });
            });
    }

    public function schedule(Schedule $schedule)  {
        $schedule->call(function () {

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
        })->everyMinute();
    }
}