<?php

namespace Mollie\Api\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;

class ApiKeyAuthenticator extends BearerTokenAuthenticator
{
    public function __construct(
        string $token
    ) {
        if (! preg_match('/^(live|test)_\w{30,}$/', trim($token))) {
            throw new InvalidAuthenticationException($token, "Invalid API key. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.");
        }

        parent::__construct($token);
    }
}
