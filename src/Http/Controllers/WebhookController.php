<?php

namespace Theaarch\Forwarder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Theaarch\Forwarder\Contracts\HandlesWebhooks;
use Theaarch\Forwarder\Events\WebhookHandled;
use Theaarch\Forwarder\Events\WebhookReceived;
use Theaarch\Forwarder\Http\Middleware\VerifyWebhookSignature;

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('forwarder.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Forwarder webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Theaarch\Forwarder\Contracts\HandlesWebhooks  $handler
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, HandlesWebhooks $handler)
    {
        WebhookReceived::dispatch($request);

        $response = $handler->handle($request);

        WebhookHandled::dispatch($request);

        return $response;
    }
}
