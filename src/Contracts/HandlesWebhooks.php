<?php

namespace Theaarch\Forwarder\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlesWebhooks
{
    /**
     * Handle a webhook call.
     *
     * @param  Request  $request
     * @return Response
     */
    public function handle(Request $request): Response;
}
