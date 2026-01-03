<?php

namespace App\Actions\Forwarder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Theaarch\Forwarder\Contracts\HandlesWebhooks;

class HandleWebhook implements HandlesWebhooks
{
    /**
     * Handle a webhook call.
     *
     * @param  Request  $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        Log::info('Webhook Received', $request->all());

        // Your logic here...

        return new Response('Webhook Handled', Response::HTTP_OK);
    }
}
