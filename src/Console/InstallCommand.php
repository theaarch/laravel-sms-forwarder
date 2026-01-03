<?php

namespace Theaarch\Forwarder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Theaarch\Forwarder\ForwarderServiceProvider;

#[AsCommand(name: 'forwarder:install')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forwarder:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Forwarder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callSilent('vendor:publish', [
            '--provider' => ForwarderServiceProvider::class,
        ]);

        $this->registerPaymentServiceProvider();

        $this->components->info('Forwarder scaffolding installed successfully.');
    }

    /**
     * Register the Forwarder service provider in the application configuration file.
     */
    protected function registerPaymentServiceProvider(): void
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            return;
        }

        ServiceProvider::addProviderToBootstrapFile(\App\Providers\ForwarderServiceProvider::class);
    }
}
