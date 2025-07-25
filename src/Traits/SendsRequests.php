<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Utils\DataTransformer;

/**
 * @mixin MollieApiClient
 */
trait SendsRequests
{
    /**
     * @return mixed
     */
    public function send(Request $request)
    {
        $pendingRequest = new PendingRequest($this, $request);
        $pendingRequest = $pendingRequest->executeRequestHandlers();

        $pendingRequest = (new DataTransformer)->transform($pendingRequest);

        try {
            $response = $this->httpClient->sendRequest($pendingRequest);

            return $pendingRequest->executeResponseHandlers($response);
        } catch (MollieException $exception) {
            $exception = $pendingRequest->executeFatalHandlers($exception);

            throw $exception;
        }
    }
}
