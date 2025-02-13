<?php

namespace Mollie\Api\Http\Auth;

use Mollie\Api\Contracts\Authenticator;
use Mollie\Api\Http\PendingRequest;

class BearerTokenAuthenticator implements Authenticator
{
    protected string $token;

    public function __construct(
        string $token
    ) {
        $this->token = trim($token);
    }

    public function authenticate(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', "Bearer {$this->token}");
    }
}
