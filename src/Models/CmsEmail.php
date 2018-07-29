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
use Illuminate\Console\Scheduling\Schedule;
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

    public static function scheduler(Schedule $schedule) {
        $schedule->call(function () {
            Campaign::whereNotNull('schedule')
                    ->whereHasSent(false)
                    ->where('schedule', '<', Carbon::now())
                    ->get()
                ->each->launch();
        })->everyMinute();
    }
}