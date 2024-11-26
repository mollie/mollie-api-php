<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HandlesResourceCreation;

class Hydrate
{
    use HandlesResourceCreation;

    public function __invoke(Response $response)
    {
        if (! $response->getRequest()::$shouldAutoHydrate || !$response->getRequest() instanceof ResourceHydratableRequest) {
            return $response;
        }

        return $this->createResource($response->getRequest(), $response);
    }
}
