<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\Response;

class ResetIdempotencyKey
{
    public function __invoke(Response $response): void
    {
        $response->getConnector()->resetIdempotencyKey();
    }
}
