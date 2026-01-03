<?php

namespace Theaarch\Forwarder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ForwarderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/forwarder.php', 'forwarder');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureRoutes();
        $this->registerCommands();
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
                __DIR__ . '/../stubs/forwarder.php' => $this->app->configPath('forwarder.php'),
            ], 'forwarder-config');

            $this->publishes([
                __DIR__.'/../stubs/HandleWebhook.php' => $this->app->basePath('app/Actions/Forwarder/HandleWebhook.php'),
                __DIR__ . '/../stubs/ForwarderServiceProvider.php' => $this->app->basePath('app/Providers/ForwarderServiceProvider.php'),
            ], 'forwarder-support');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes(): void
    {
        if (Forwarder::$registersRoutes) {
            Route::group([
                'domain' => config('forwarder.domain', null),
                'prefix' => config('forwarder.prefix'),
                'as' => 'forwarder.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
            });
        }
    }

    /**
     * Register the package's commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}
