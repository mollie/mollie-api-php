<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceFactory;

trait HandlesResourceHydration
{
    /**
     * @return mixed
     */
    protected function hydrate(ResourceHydratableRequest $request, Response $response)
    {
        $targetResourceClass = $request->getTargetResourceClass();

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

        return ResourceFactory::createBaseResourceCollection(
            $response->getConnector(),
            ($targetCollectionClass)::getResourceClass(),
            $result->_embedded->{$targetCollectionClass::getCollectionResourceName()},
            $result->_links,
            $targetCollectionClass,
            $response,
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
