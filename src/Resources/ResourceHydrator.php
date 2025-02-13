<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;

class ResourceHydrator
{
    /**
     * Hydrate a response into a resource or collection
     *
     * @return Response|BaseResource|BaseCollection|LazyCollection|IsWrapper
     */
    public function hydrate(ResourceHydratableRequest $request, Response $response)
    {
        $targetResourceClass = $request->getHydratableResource();

        if ($targetResourceClass instanceof WrapperResource) {
            $response = $this->hydrate(
                // Reset the hydratable resource to the original resource class.
                $request->resetHydratableResource(),
                $response,
            );

            return ResourceFactory::createDecoratedResource($response, $targetResourceClass->getWrapper());
        }

        if ($this->isCollectionTarget($targetResourceClass)) {
            $collection = $this->buildResultCollection($response, $targetResourceClass);

            return $this->unwrapIterator($request, $collection);
        }

        if ($this->isResourceTarget($targetResourceClass)) {
            return ResourceFactory::createFromApiResult($response->getConnector(), $response, $targetResourceClass);
        }

        return $response;
    }

    /**
     * @return BaseCollection|LazyCollection
     */
    private function unwrapIterator(Request $request, BaseCollection $collection)
    {
        if ($request instanceof IsIteratable && $request->iteratorEnabled()) {
            /** @var CursorCollection $collection */
            return $collection->getAutoIterator($request->iteratesBackwards());
        }

        return $collection;
    }

    private function buildResultCollection(Response $response, string $targetCollectionClass): BaseCollection
    {
        $result = $response->json();

        return ResourceFactory::createResourceCollection(
            $response->getConnector(),
            $targetCollectionClass,
            $response,
            $result->_embedded->{$targetCollectionClass::getCollectionResourceName()},
            $result->_links,
        );
    }

    private function isCollectionTarget(string $targetResourceClass): bool
    {
        return is_subclass_of($targetResourceClass, BaseCollection::class);
    }

    private function isResourceTarget(string $targetResourceClass): bool
    {
        return is_subclass_of($targetResourceClass, BaseResource::class);
    }
}
