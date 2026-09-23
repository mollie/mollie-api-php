<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Config;
use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Str;

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
     * @return Response|BaseResource|ResourceCollection|LazyCollection|IsWrapper
     */
    public function resolve(ResourceHydratableRequest $request, Response $response)
    {
        $targetResourceClass = $request->getHydratableResourceTarget();
        $wrapper = $request->getHydratableResourceWrapper();

        if ($targetResourceClass !== null) {
            $this->ensureValidTarget($targetResourceClass);
        }

        $resolved = $this->resolveTarget($request, $response, $targetResourceClass);

        return $wrapper === null
            ? $resolved
            : ResourceFactory::createDecoratedResource($resolved, $wrapper->getWrapper());
    }

    /**
     * @param  class-string<BaseResource|ResourceCollection>|null  $targetResourceClass
     * @return Response|BaseResource|ResourceCollection|LazyCollection
     */
    private function resolveTarget(ResourceHydratableRequest $request, Response $response, ?string $targetResourceClass)
    {
        if ($targetResourceClass === null) {
            return $response;
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

    private function ensureValidTarget(string $targetResourceClass): void
    {
        if (! $this->isCollectionTarget($targetResourceClass) && ! $this->isResourceTarget($targetResourceClass)) {
            throw new \InvalidArgumentException(
                "Hydratable resource class '{$targetResourceClass}' must extend ".BaseResource::class.' or '.ResourceCollection::class.'.'
            );
        }
    }

    /**
     * @param  class-string<ResourceCollection>  $targetCollectionClass
     */
    private function resolveCollection(Response $response, string $targetCollectionClass): ResourceCollection
    {
        $result = $response->json();

        $collection = ResourceFactory::createCollection(
            $response->getConnector(),
            $targetCollectionClass
        );

        $kebabCollectionKey = Config::resourceRegistry()->pluralOf($targetCollectionClass::getResourceClass());

        $data = isset($result->_embedded->{$kebabCollectionKey})
            ? $result->_embedded->{$kebabCollectionKey}
            : $result->_embedded->{Str::snake($kebabCollectionKey)};

        return $this->hydrator->hydrateCollection(
            $collection,
            $data,
            $response,
            $result->_links
        );
    }

    private function unwrapIterator(Request $request, ResourceCollection $collection)
    {
        if ($request instanceof IsIteratable && $request->iteratorEnabled()) {
            /** @var CursorCollection $collection */
            return $collection->getAutoIterator($request->iteratesBackwards());
        }

        return $collection;
    }

    /**
     * @phpstan-assert-if-true class-string<ResourceCollection> $targetResourceClass
     */
    private function isCollectionTarget(string $targetResourceClass): bool
    {
        return is_subclass_of($targetResourceClass, ResourceCollection::class);
    }

    private function isResourceTarget(string $targetResourceClass): bool
    {
        return is_subclass_of($targetResourceClass, BaseResource::class);
    }
}
