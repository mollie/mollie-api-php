<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Http\Requests\Request;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceFactory;

/**
 * @mixin Endpoint
 */
trait HandlesResourceBuilding
{
    protected function build(Request $request, Response $response): mixed
    {
        $decodedResponse = $response->decode();
        $targetResourceClass = $request->getTargetResourceClass();

        if ($this->isCollectionTarget($targetResourceClass)) {
            $collection = $this->buildResultCollection($decodedResponse, $targetResourceClass);

            return $this->unwrapIterator($request, $collection);
        }

        if ($this->isResourceTarget($targetResourceClass)) {
            return ResourceFactory::createFromApiResult($this->client, $decodedResponse, $targetResourceClass);
        }

        return null;
    }

    private function unwrapIterator(Request $request, BaseCollection $collection): BaseCollection|LazyCollection
    {
        if ($request instanceof IsIteratable && $request->iteratorEnabled()) {
            /** @var CursorCollection $collection */
            return $collection->getAutoIterator($request->iteratesBackwards());
        }

        return $collection;
    }

    private function buildResultCollection(object $result, string $targetCollectionClass): BaseCollection
    {
        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            ($targetCollectionClass)::getResourceClass(),
            $result->_embedded->{$targetCollectionClass::getCollectionResourceName()},
            $result->_links,
            $targetCollectionClass
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
