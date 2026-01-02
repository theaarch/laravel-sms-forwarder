<?php

namespace App\Actions\SmsForwarder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;

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
        Log::info('SmsForwarder Webhook', $request->all());

        // Your logic here...

        return new Response('Webhook Handled', Response::HTTP_OK);
    }
}
