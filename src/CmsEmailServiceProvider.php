<?php

namespace Anacreation\CmsEmail;

use Anacreation\Cms\Models\Cms;
use Anacreation\CmsEmail\Models\CmsEmail;
use Anacreation\Notification\Provider\Contracts\EmailSender;
use Anacreation\Notification\Provider\ServiceProviders\SendGrid;
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
            __DIR__ . '/views' => resource_path('views/vendor/cms_email'),
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
