<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Http\Response;

class ResetIdempotencyKey implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        $response->getConnector()->resetIdempotencyKey();
    }
}
