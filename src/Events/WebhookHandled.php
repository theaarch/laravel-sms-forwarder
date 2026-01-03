<?php

namespace Theaarch\Forwarder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class WebhookHandled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request
    )
    {
        //
    }
}
