<?php

namespace App\Providers;

use App\Actions\Forwarder\HandleWebhook;
use Illuminate\Support\ServiceProvider;
use Theaarch\Forwarder\Forwarder;

class ForwarderServiceProvider extends ServiceProvider
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
        Forwarder::handleWebhookUsing(HandleWebhook::class);
    }
}
