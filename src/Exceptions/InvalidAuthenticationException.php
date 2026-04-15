<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

class InvalidAuthenticationException extends MollieException
{
    public function __construct(
        public readonly string $token,
        string $message = ''
    ) {
        parent::__construct($message !== '' ? $message : "Invalid authentication token: '{$token}'");
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
