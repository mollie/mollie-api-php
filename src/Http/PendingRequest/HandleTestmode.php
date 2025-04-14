<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Contracts\SupportsTestmode;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\PendingRequest;

class HandleTestmode
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $connector = $pendingRequest->getConnector();
        $authenticator = $connector->getAuthenticator();

        if ($authenticator instanceof ApiKeyAuthenticator) {
            $this->removeTestmode($pendingRequest);
        } elseif ($connector->getTestmode() || $pendingRequest->getRequest()->getTestmode()) {
            $this->applyTestmode($pendingRequest);
        }

        return $pendingRequest;
    }

    private function applyTestmode(PendingRequest $pendingRequest): void
    {
        $request = $pendingRequest->getRequest();

        if (! $request instanceof SupportsTestmode) {
            return;
        }

        if ($request instanceof SupportsTestmodeInQuery) {
            $pendingRequest->query()->add('testmode', true);
        } elseif ($request instanceof SupportsTestmodeInPayload) {
            /** @var PayloadRepository $payload */
            $payload = $pendingRequest->payload();

            $payload->add('testmode', true);
        }
    }

    private function removeTestmode(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof SupportsTestmodeInQuery) {
            $pendingRequest->query()->remove('testmode');
        }

        if (! $pendingRequest->getRequest() instanceof SupportsTestmodeInPayload) {
            return;
        }

        $payload = $pendingRequest->payload();

        if ($payload === null) {
            return;
        }

        $payload->remove('testmode');
    }
}
