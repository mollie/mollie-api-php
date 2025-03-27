<?php

namespace Mollie\Api\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;

class ApiKeyAuthenticator extends BearerTokenAuthenticator
{
    private bool $isTestToken = false;

    public function __construct(
        string $token
    ) {
        if (! preg_match('/^(live|test)_\w{30,}$/', trim($token))) {
            throw new InvalidAuthenticationException($token, "Invalid API key. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.");
        }

        $this->isTestToken = strpos($token, 'test_') === 0;

        parent::__construct($token);
    }

    public function isTestToken(): bool
    {
        return $this->isTestToken;
    }
}
