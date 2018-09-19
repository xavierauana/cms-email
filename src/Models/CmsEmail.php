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
use Anacreation\CmsEmail\Controllers\WebhooksController;
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
                Route::post('email/webhook',
                    WebhooksController::class . "@parse")
                     ->name('email.webhook');

                Route::post('email/lists/{list}/registration',
                    CampaignsController::class . "@registration")
                     ->name('email.lists.registration');

                Route::get('email/campaigns/{campaign}',
                    CampaignsController::class . "@show")->name('campaign.web');

                Route::get('lists/{list}/recipients/unsubscribe',
                    EmailListRecipientsController::class . "@unsubscribe")
                     ->name('lists.unsubscribe');
                Route::get('lists/{list}/recipients/confirm',
                    EmailListRecipientsController::class . "@confirm")
                     ->name('lists.confirm');

                Route::group(['prefix' => config('admin.route_prefix')],
                    function () {
                        Route::group([
                            'middleware' => 'auth:admin',
                            'prefix'     => 'email'
                        ],
                            function () {

                                Route::get('campaigns/{campaign}/activities',
                                    CampaignsController::class . "@activities")
                                     ->name('campaigns.activities');
                                Route::get('campaigns/{campaign}/activities/details/{status}',
                                    CampaignsController::class . "@details")
                                     ->name('campaigns.activities.details');
                                Route::post('campaigns/{campaign}/details/resend_failed',
                                    CampaignsController::class . "@resendAll")
                                     ->name('campaigns.resend.all');
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

                                Route::get('lists/{list}/recipients/export',
                                    EmailListRecipientsController::class . "@export")
                                     ->name('lists.recipients.export');

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
}