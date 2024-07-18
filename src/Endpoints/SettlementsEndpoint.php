<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;

class SettlementsEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "settlements";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Settlement::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = SettlementCollection::class;

    /**
     * Retrieve a single settlement from Mollie.
     *
     * Will throw a ApiException if the settlement id is invalid or the resource cannot be found.
     *
     * @param string $settlementId
     * @param array $parameters
     *
     * @return Settlement
     * @throws ApiException
     */
    public function get(string $settlementId, array $parameters = []): Settlement
    {
        /** @var Settlement */
        return $this->readResource($settlementId, $parameters);
    }

    /**
     * Retrieve the details of the current settlement that has not yet been paid out.
     *
     * @return Settlement
     * @throws ApiException
     */
    public function next(): Settlement
    {
        /** @var Settlement */
        return $this->readResource("next", []);
    }

    /**
     * Retrieve the details of the open balance of the organization.
     *
     * @return Settlement
     * @throws ApiException
     */
    public function open(): Settlement
    {
        /** @var Settlement */
        return $this->readResource("open", []);
    }

    /**
     * Retrieves a collection of Settlements from Mollie.
     *
     * @param string $from The first settlement ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return SettlementCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): SettlementCollection
    {
        /** @var SettlementCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over settlements retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
