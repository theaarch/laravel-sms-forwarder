<?php

return [

    'domain' => env('FORWARDER_DOMAIN'),

    'prefix' => env('FORWARDER_PREFIX', 'forwarder'),

    'middleware' => null,

    'webhook' => [
        'secret' => env('FORWARDER_WEBHOOK_SECRET'),
        'tolerance' => env('FORWARDER_WEBHOOK_TOLERANCE', 300),
    ],

];
