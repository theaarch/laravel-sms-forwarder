<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Theaarch\Forwarder\Contracts\HandlesWebhooks;
use Theaarch\Forwarder\Events\WebhookHandled;
use Theaarch\Forwarder\Events\WebhookReceived;
use Theaarch\Forwarder\Forwarder;

it('can handle webhook calls', function () {
    Event::fake();

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(
        response('ok')
    );

    Forwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $raw = 'foo=bar';
    $response = $this->call(
        method: 'POST',
        uri: route('forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertOk();

    parse_str($raw, $payload);

    Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
        /** @var \Illuminate\Http\Request $request */
        $request = $event->request;
        parse_str($request->getContent(), $data);

        return $data['foo'] == $payload['foo'];
    });

    Event::assertDispatched(WebhookHandled::class, function ($event) use ($payload) {
        /** @var \Illuminate\Http\Request $request */
        $request = $event->request;
        parse_str($request->getContent(), $data);

        return $data['foo'] == $payload['foo'];
    });
});

it('verifies webhook signature when secret is set', function () {
    $secret = 'test-secret';

    Config::set('forwarder.webhook.secret', $secret);

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(
        response('ok')
    );

    Forwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $timestamp = round((microtime(true) * 1000));

    $signedPayload = "{$timestamp}\n{$secret}";
    $binary = hash_hmac('sha256', $signedPayload, $secret, true);
    $signature = urlencode(base64_encode($binary));

    $raw = "foo=bar&timestamp={$timestamp}&sign=".urlencode($signature);
    $response = $this->call(
        method: 'POST',
        uri: route('forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertOk();
});

it('fails when signature is missing but secret is set', function () {
    Config::set('forwarder.webhook.secret', 'test-secret');

    $raw = "foo=bar";
    $response = $this->call(
        method: 'POST',
        uri: route('forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertStatus(403);
});

it('fails when signature is invalid', function () {
    Config::set('forwarder.webhook.secret', 'test-secret');

    $timestamp = time();
    $secret = 'wrong-secret';

    $signedPayload = "{$timestamp}\n{$secret}";
    $binary = hash_hmac('sha256', $signedPayload, $secret, true);
    $signature = urlencode(base64_encode($binary));

    $raw = "foo=bar&timestamp={$timestamp}&sign=".urlencode($signature);
    $response = $this->call(
        method: 'POST',
        uri: route('forwarder.webhook'),
        server: [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => strlen($raw),
        ],
        content: $raw
    );

    $response->assertStatus(403);
});
