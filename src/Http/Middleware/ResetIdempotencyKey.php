<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Http\Response;

class ResetIdempotencyKey implements ResponseMiddleware
{
    /**
     * @param  Response|ViableResponse|mixed  $response
     */
    public function __invoke($response): void
    {
        $response->getConnector()->resetIdempotencyKey();
    }
}
