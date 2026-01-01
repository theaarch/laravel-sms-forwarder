<?php

namespace Theaarch\SmsForwarder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Theaarch\SmsForwarder\SmsForwarderServiceProvider;

#[AsCommand(name: 'sms-forwarder:install')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms-forwarder:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the SmsForwarder';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->callSilent('vendor:publish', [
            '----provider' => SmsForwarderServiceProvider::class,
        ]);

        $this->registerPaymentServiceProvider();

        $this->components->info('SmsForwarder scaffolding installed successfully.');
    }

    /**
     * Register the SmsForwarder service provider in the application configuration file.
     */
    protected function registerPaymentServiceProvider(): void
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            return;
        }

        ServiceProvider::addProviderToBootstrapFile(\App\Providers\SmsForwarderServiceProvider::class);
    }
}
