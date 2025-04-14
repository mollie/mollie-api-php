<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\ResourceHydrator;

class Hydrate implements ResponseMiddleware
{
    public function __invoke(Response $response)
    {
        $request = $response->getRequest();

        if (! $response->isEmpty() && $request instanceof ResourceHydratableRequest && $request->isHydratable()) {
            return (new ResourceHydrator)->hydrate($request, $response);
        }

        return $response;
    }
}
