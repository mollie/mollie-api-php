<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;

class ResourceResolver
{
    private ResourceHydrator $hydrator;

    public function __construct(ResourceHydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Resolve a response into the appropriate resource type.
     *
     * @return Response|BaseResource|BaseCollection|LazyCollection|IsWrapper
     */
    public function resolve(ResourceHydratableRequest $request, Response $response)
    {
        $targetResourceClass = $request->getHydratableResource();

        if ($targetResourceClass instanceof WrapperResource) {
            $response = $this->resolve(
                $request->resetHydratableResource(),
                $response
            );

            return ResourceFactory::createDecoratedResource($response, $targetResourceClass->getWrapper());
        }

        if ($this->isCollectionTarget($targetResourceClass)) {
            $collection = $this->resolveCollection($response, $targetResourceClass);

            return $this->unwrapIterator($request, $collection);
        }

        if ($this->isResourceTarget($targetResourceClass)) {
            $resource = ResourceFactory::create($response->getConnector(), $targetResourceClass);

            return $this->hydrator->hydrate($resource, $response->json(), $response);
        }

        return $response;
    }

    private function resolveCollection(Response $response, string $targetCollectionClass): BaseCollection
    {
        $result = $response->json();
        $collection = ResourceFactory::createCollection(
            $response->getConnector(),
            $targetCollectionClass
        );

        return $this->hydrator->hydrateCollection(
            $collection,
            $result->_embedded->{$targetCollectionClass::getCollectionResourceName()},
            $response,
            $result->_links
        );
    }

    private function unwrapIterator(Request $request, BaseCollection $collection)
    {
        if ($request instanceof IsIteratable && $request->iteratorEnabled()) {
            /** @var CursorCollection $collection */
            return $collection->getAutoIterator($request->iteratesBackwards());
        }

        return $collection;
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
