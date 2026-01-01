<?php

namespace Theaarch\SmsForwarder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SmsForwarderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sms_forwarder.php', 'sms_forwarder');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureRoutes();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/sms_forwarder.php' => config_path('sms_forwarder.php'),
            ], 'sms-forwarder-config');

            $this->publishes([
                __DIR__.'/../stubs/HandleWebhook.php' => app_path('Actions/SmsForwarder/HandleWebhook.php'),
                __DIR__.'/../stubs/SmsForwarderServiceProvider.php' => app_path('Providers/SmsForwarderServiceProvider.php'),
            ], 'sms-forwarder-support');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes(): void
    {
        if (SmsForwarder::$registersRoutes) {
            Route::group([
                'domain' => config('sms_forwarder.domain', null),
                'prefix' => config('sms_forwarder.prefix'),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
            });
        }
    }
}
