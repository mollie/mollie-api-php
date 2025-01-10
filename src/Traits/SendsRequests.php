<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @mixin MollieApiClient
 */
trait SendsRequests
{
    public function send(Request $request): object
    {
        $pendingRequest = new PendingRequest($this, $request);
        $pendingRequest = $pendingRequest->executeRequestHandlers();

        try {
            $response = $this->httpClient->sendRequest($pendingRequest);

            return $pendingRequest->executeResponseHandlers($response);
        } catch (RequestException $e) {
            throw $e;
        }
    }
}
