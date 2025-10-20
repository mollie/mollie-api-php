<?php

namespace Mollie\Api\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;

class AccessTokenAuthenticator extends BearerTokenAuthenticator
{
    public function __construct(
        string $token
    ) {
        if (! TokenValidator::isAccessToken($token)) {
            throw new InvalidAuthenticationException($token, "Invalid OAuth access token. An access token must start with 'access_'.");
        }

        parent::__construct($token);
    }
}
