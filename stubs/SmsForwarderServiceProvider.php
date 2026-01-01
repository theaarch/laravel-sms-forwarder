<?php

namespace App\Providers;

use App\Actions\SmsForwarder\HandleWebhook;
use Illuminate\Support\ServiceProvider;
use Theaarch\SmsForwarder\SmsForwarder;

class SmsForwarderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        SmsForwarder::handleWebhookUsing(HandleWebhook::class);
    }
}
