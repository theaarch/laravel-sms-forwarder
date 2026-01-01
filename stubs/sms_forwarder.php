<?php

return [

    'domain' => env('SMS_FORWARDER_DOMAIN'),

    'prefix' => env('SMS_FORWARDER_PREFIX', 'sms-forwarder'),

    'webhook' => [
        'secret' => env('SMS_FORWARDER_WEBHOOK_SECRET'),
        'tolerance' => env('SMS_FORWARDER_WEBHOOK_TOLERANCE', 300),
    ],

];
