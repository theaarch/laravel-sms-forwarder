<?php

namespace Theaarch\SmsForwarder;

use Theaarch\SmsForwarder\Exceptions\SignatureVerificationException;

class WebhookSignature
{
    /**
     * Verifies the signature payload sent by SmsForwarder.
     *
     * @param  string  $payload
     * @param  string  $secret
     * @param  int|null  $tolerance
     * @return bool
     *
     * @throws SignatureVerificationException
     */
    public static function verifyPayload(string $payload, string $secret, int $tolerance = null): bool
    {
        $data = self::parsePayload($payload);

        $timestamp = $data['timestamp'] ?? null;
        $signature = $data['sign'] ?? null;

        if (empty($timestamp) || empty($signature)) {
            throw SignatureVerificationException::factory(
                'Unable to extract timestamp and signatures from payload',
                $payload
            );
        }

        $signedPayload = "{$timestamp}\n{$secret}";
        $expectedSignature = self::computeSignature($signedPayload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            if (app()->hasDebugModeEnabled()) {
                app('log')->debug('Invalid signature', [
                    'payload' => $payload,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                    'expected_Signature' => $expectedSignature,
                ]);
            }

            throw SignatureVerificationException::factory(
                'No signatures found matching the expected signature for payload',
                $payload
            );
        }

        // Check if timestamp is within tolerance
        $t = (int) (microtime(true) * 1000);
        if (($tolerance > 0) && (abs($t - $timestamp) > $tolerance)) {
            throw SignatureVerificationException::factory(
                'Timestamp outside the tolerance zone',
                $payload
            );
        }

        return true;
    }

    /**
     * Computes the signature for a given payload and secret.
     *
     * @param  string  $payload
     * @param  string  $secret
     * @return string
     */
    private static function computeSignature(string $payload, string $secret): string
    {
        $binary = hash_hmac('sha256', $payload, $secret, true);

        return urlencode(base64_encode($binary));
    }

    private static function parsePayload(string $payload): array
    {
        // $data = [];
        // $items = explode('&', $payload);
        //
        // foreach ($items as $item) {
        //     [$key, $value] = explode('=', $item, 2);
        //     $data[$key] = $value;
        // }

        parse_str($payload, $data);

        return $data;
    }
}
