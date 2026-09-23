<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

class InvalidAuthenticationException extends MollieException
{
    public function __construct(
        public readonly string $token,
        string $message = ''
    ) {
        parent::__construct($this->resolveMessage($token, $message));
    }

    public function getToken(): string
    {
        return $this->token;
    }

    private function resolveMessage(string $token, string $message): string
    {
        if ($message === '') {
            return 'Invalid authentication token.';
        }

        return $token === '' ? $message : str_replace($token, '[redacted]', $message);
    }
}
