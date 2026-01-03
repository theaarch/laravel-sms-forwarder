<?php

namespace Theaarch\Forwarder\Exceptions;

use Exception;

class SignatureVerificationException extends Exception implements ExceptionInterface
{
    protected ?string $httpBody;

    /**
     * Creates a new SignatureVerificationException exception.
     *
     * @param  string  $message the exception message
     * @param  string|null  $httpBody the HTTP body as a string
     *
     * @return SignatureVerificationException
     */
    public static function factory(
        string $message,
        string $httpBody = null,
    ): SignatureVerificationException
    {
        $instance = new static($message);
        $instance->setHttpBody($httpBody);

        return $instance;
    }

    /**
     * Gets the HTTP body as a string.
     *
     * @return null|string
     */
    public function getHttpBody(): ?string
    {
        return $this->httpBody;
    }

    /**
     * Sets the HTTP body as a string.
     *
     * @param  string|null  $httpBody
     */
    public function setHttpBody(?string $httpBody): void
    {
        $this->httpBody = $httpBody;
    }
}
