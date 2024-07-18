<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\HasBody;
use Mollie\Api\Http\Request;
use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\ResourceFactory;

abstract class BaseEndpointCollection
{
    /**
     * @var MollieApiClient
     */
    protected MollieApiClient $client;

    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }

    public function send(Request $request): mixed
    {
        $path = $request->resolveResourcePath()
            . $this->buildQueryString($request->getQuery());

        $body = $request instanceof HasBody
            ? $request->getBody()
            : null;

        $result = $this->client->performHttpCall(
            $request->getMethod(),
            $path,
            $body
        );

        if ($result->isEmpty()) {
            return null;
        }

        $targetResourceClass = $request->getTargetResourceClass();

        if (is_subclass_of($targetResourceClass, BaseCollection::class)) {
            $collection = $this->buildResultCollection($result->decode(), $targetResourceClass);

            if ($request instanceof IsIteratable && $request->iteratorEnabled()) {
                /** @var CursorCollection $collection */
                return $collection->getAutoIterator($request->iteratesBackwards());
            }

            return $collection;
        }

        if (is_subclass_of($targetResourceClass, BaseResource::class)) {
            return ResourceFactory::createFromApiResult($this->client, $result->decode(), $targetResourceClass);
        }

        return null;
    }

    protected function buildResultCollection(object $result, string $targetCollectionClass): BaseCollection
    {
        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            ($targetCollectionClass)::getResourceClass(),
            $result->_embedded->{$targetCollectionClass::getCollectionResourceName()},
            $result->_links,
            $targetCollectionClass
        );
    }

    /**
     * Create a generator for iterating over a resource's collection using REST API calls.
     *
     * This function fetches paginated data from a RESTful resource endpoint and returns a generator
     * that allows you to iterate through the items in the collection one by one. It supports forward
     * and backward iteration, pagination, and filtering.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     * @return LazyCollection
     */
    protected function createIterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        /** @var CursorCollection $page */
        $page = $this->fetchCollection($from, $limit, $filters);

        return $page->getAutoIterator($iterateBackwards);
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return "";
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = "true";
            }

            if ($value === false) {
                $filters[$key] = "false";
            }
        }

        return "?" . http_build_query($filters, "", "&");
    }
}
