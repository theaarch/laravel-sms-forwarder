<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;
use Theaarch\SmsForwarder\Events\WebhookHandled;
use Theaarch\SmsForwarder\Events\WebhookReceived;
use Theaarch\SmsForwarder\SmsForwarder;

it('can handle webhook calls', function () {
    Event::fake();

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(
        response('ok')
    );

    SmsForwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $raw = 'foo=bar';
    $response = $this->call(
        method: 'POST',
        uri: route('sms_forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertOk();

    parse_str($raw, $payload);

    Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
        return $event->payload['foo'] == $payload['foo'];
    });

    Event::assertDispatched(WebhookHandled::class, function ($event) use ($payload) {
        return $event->payload['foo'] == $payload['foo'];
    });
});

it('verifies webhook signature when secret is set', function () {
    $secret = 'test-secret';

    Config::set('sms_forwarder.webhook.secret', $secret);

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(
        response('ok')
    );

    SmsForwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $timestamp = round((microtime(true) * 1000));

    $signedPayload = "{$timestamp}\n{$secret}";
    $binary = hash_hmac('sha256', $signedPayload, $secret, true);
    $signature = urlencode(base64_encode($binary));

    $raw = "foo=bar&timestamp={$timestamp}&sign=".urlencode($signature);
    $response = $this->call(
        method: 'POST',
        uri: route('sms_forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertOk();
});

it('fails when signature is missing but secret is set', function () {
    Config::set('sms_forwarder.webhook.secret', 'test-secret');

    $raw = "foo=bar";
    $response = $this->call(
        method: 'POST',
        uri: route('sms_forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertStatus(403);
});

it('fails when signature is invalid', function () {
    Config::set('sms_forwarder.webhook.secret', 'test-secret');

    $timestamp = time();
    $secret = 'wrong-secret';

    $signedPayload = "{$timestamp}\n{$secret}";
    $binary = hash_hmac('sha256', $signedPayload, $secret, true);
    $signature = urlencode(base64_encode($binary));

    $raw = "foo=bar&timestamp={$timestamp}&sign=".urlencode($signature);
    $response = $this->call(
        method: 'POST',
        uri: route('sms_forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertStatus(403);
});
