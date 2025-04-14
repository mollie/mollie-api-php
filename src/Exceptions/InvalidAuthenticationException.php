<?php

namespace Mollie\Api\Exceptions;

class InvalidAuthenticationException extends MollieException
{
    private string $token;

    public function __construct(string $token, string $message = '')
    {
        $this->token = $token;
        parent::__construct($message ?: "Invalid authentication token: '{$token}'");
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
