<?php

namespace Mollie\Api\Http\Auth;

use Mollie\Api\Exceptions\ApiException;

class AccessTokenAuthenticator extends BearerTokenAuthenticator
{
    public function __construct(
        string $token,
    ) {
        if (! preg_match('/^access_\w+$/', trim($token))) {
            throw new ApiException("Invalid OAuth access token: '{$token}'. An access token must start with 'access_'.");
        }

        parent::__construct($token);
    }
}
