<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HandlesResourceHydration;

class Hydrate
{
    use HandlesResourceHydration;

    public function __invoke(Response $response)
    {
        $request = $response->getRequest();

        if (! $request instanceof ResourceHydratableRequest || ! $request->shouldAutoHydrate()) {
            return $response;
        }

        return $this->hydrate($request, $response);
    }
}
