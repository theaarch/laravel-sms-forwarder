<?php

namespace Theaarch\Forwarder;

use Theaarch\Forwarder\Contracts\HandlesWebhooks;

class Forwarder
{
    /**
     * Indicates if Forwarder routes will be registered.
     *
     * @var bool
     */
    public static bool $registersRoutes = true;

    /**
     * Register a class / callback that should be used to handle new webhooks.
     *
     * @param  callable|string  $callback
     * @return void
     */
    public static function handleWebhookUsing(callable|string $callback): void
    {
        app()->singleton(HandlesWebhooks::class, $callback);
    }

    /**
     * Configure Forwarder to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }
}
