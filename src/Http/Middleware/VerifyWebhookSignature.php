<?php

namespace Theaarch\SmsForwarder\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Theaarch\SmsForwarder\Exceptions\SignatureVerificationException;
use Theaarch\SmsForwarder\WebhookSignature;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            WebhookSignature::verifyPayload(
                $request->getContent(),
                config('sms_forwarder.webhook.secret'),
                config('sms_forwarder.webhook.tolerance')
            );
        } catch (SignatureVerificationException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        return $next($request);
    }
}
