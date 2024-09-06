<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class GuardResponse
{
    public function __invoke(Response $response)
    {
        if (empty($response->body()) && $response->status() !== ResponseStatusCode::HTTP_NO_CONTENT) {
            throw new ApiException('No response body found.');
        }

        if (empty($response->body())) {
            return;
        }

        $response->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$response->body()}'.");
        }

        // @todo check if this is still necessary as it seems to be from api v1
        if (isset($response->json()->error)) {
            throw new ApiException($response->json()->error->message);
        }
    }
}
