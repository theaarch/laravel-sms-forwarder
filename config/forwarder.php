<?php

return [

    'domain' => null,

    'prefix' => env('FORWARDER_PREFIX', 'sms-forwarder'),

    'middleware' => null,

    'webhook' => [
        'secret' => env('FORWARDER_WEBHOOK_SECRET'),
        'tolerance' => env('FORWARDER_WEBHOOK_TOLERANCE', 300),
    ],

];
