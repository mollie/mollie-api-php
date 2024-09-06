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
    use HandlesResourceCreation;

    public function send(Request $request): ?object
    {
        $pendingRequest = new PendingRequest($this, $request);

        // Execute request middleware
        $pendingRequest->executeRequestHandlers();

        $response = $this->httpClient->sendRequest($pendingRequest);

        // Execute response middleware
        $response = $pendingRequest->executeResponseHandlers($response);

        return $this->createResource($request, $response);
    }
}
