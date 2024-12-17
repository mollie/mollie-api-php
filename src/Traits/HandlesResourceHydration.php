<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceHydrator;

trait HandlesResourceHydration
{
    protected ResourceHydrator $resourceHydrator;

    public function initializeHandlesResourceHydration()
    {
        $this->resourceHydrator = new ResourceHydrator;
    }

    /**
     * Hydrate the response if the request is a ResourceHydratableRequest.
     *
     * @return Response|BaseResource|BaseCollection|LazyCollection
     */
    public function hydrateIfApplicable(Response $response)
    {
        $request = $response->getRequest();

        if ($request instanceof ResourceHydratableRequest && $request->isHydratable()) {
            return $this->resourceHydrator->hydrate($request, $response);
        }

        return $response;
    }
}
