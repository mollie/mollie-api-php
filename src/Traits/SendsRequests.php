<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;

/**
 * @mixin MollieApiClient
 */
trait SendsRequests
{
    public function send(Request $request): object
    {
        $pendingRequest = new PendingRequest($this, $request);

        $pendingRequest = $pendingRequest->executeRequestHandlers();

        return $pendingRequest->executeResponseHandlers(
            $this->httpClient->sendRequest($pendingRequest)
        );
    }
}
