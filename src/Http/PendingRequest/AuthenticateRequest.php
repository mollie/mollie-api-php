<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\PendingRequest;

class AuthenticateRequest
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $authenticator = $pendingRequest->getConnector()->getAuthenticator();

        if (! $authenticator) {
            throw new ApiException('You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.');
        }

        /**
         * Remove testmode parameter from the request if authenticated via ApiKey.
         */
        if ($authenticator instanceof ApiKeyAuthenticator) {
            $this->removeTestmode($pendingRequest);
        }

        $authenticator->authenticate($pendingRequest);

        return $pendingRequest;
    }

    private function removeTestmode(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->query()->has('testmode')) {
            $pendingRequest->query()->remove('testmode');
        }

        if ($pendingRequest->getRequest() instanceof HasPayload) {
            $pendingRequest->payload()->remove('testmode');
        }
    }
}
