<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Exceptions\MissingAuthenticationException;
use Mollie\Api\Http\PendingRequest;

class AuthenticateRequest
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $authenticator = $pendingRequest->getConnector()->getAuthenticator();

        if (! $authenticator) {
            throw new MissingAuthenticationException;
        }

        $authenticator->authenticate($pendingRequest);

        return $pendingRequest;
    }
}
