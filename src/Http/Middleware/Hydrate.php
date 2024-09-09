<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HandlesResourceCreation;

class Hydrate
{
    use HandlesResourceCreation;

    public function __invoke(Response $response)
    {
        if (! $response->getRequest()::$shouldAutoHydrate) {
            return $response;
        }

        return $this->createResource($response->getRequest(), $response);
    }
}
