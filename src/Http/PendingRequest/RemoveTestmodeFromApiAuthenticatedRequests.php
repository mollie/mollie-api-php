<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\PendingRequest;

class RemoveTestmodeFromApiAuthenticatedRequests
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $authenticator = $pendingRequest->getConnector()->getAuthenticator();

        if ($authenticator instanceof ApiKeyAuthenticator) {
            $this->removeTestmode($pendingRequest);
        }

        return $pendingRequest;
    }

    private function removeTestmode(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof SupportsTestmodeInQuery) {
            $pendingRequest->query()->remove('testmode');
        }

        if (!$pendingRequest->getRequest() instanceof SupportsTestmodeInPayload) {
            return;
        }

        $payload = $pendingRequest->payload();

        if ($payload === null) {
            return;
        }

        $payload->remove('testmode');
    }
}
