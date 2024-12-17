<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class GuardResponse implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        if (($isEmpty = $response->isEmpty()) && $response->status() !== ResponseStatusCode::HTTP_NO_CONTENT) {
            throw new ApiException('No response body found.');
        }

        if ($isEmpty) {
            return;
        }

        $data = $response->json();

        // @todo check if this is still necessary as it seems to be from api v1
        if (isset($data->error)) {
            throw new ApiException($data->error->message);
        }
    }
}
