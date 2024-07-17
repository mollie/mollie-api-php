<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Resources\LazyCollection;

class BalanceEndpoint extends EndpointCollection
{
    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Balance::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = BalanceCollection::class;

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     *
     * @var string
     */
    protected static string $resourceIdPrefix = 'bal_';

    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "balances";

    /**
     * Retrieve a single balance from Mollie.
     *
     * Will throw an ApiException if the balance id is invalid or the resource cannot be found.
     *
     * @param string $balanceId
     * @param array $parameters
     * @return Balance
     * @throws ApiException
     */
    public function get(string $balanceId, array $parameters = []): Balance
    {
        $this->guardAgainstInvalidId($balanceId);

        /** @var Balance */
        return $this->readResource($balanceId, $parameters);
    }

    /**
     * Retrieve the primary balance from Mollie.
     *
     * Will throw an ApiException if the balance id is invalid or the resource cannot be found.
     *
     * @param array $parameters
     * @return \Mollie\Api\Resources\Balance
     * @throws ApiException
     */
    public function primary(array $parameters = []): Balance
    {
        /** @var Balance */
        return $this->readResource("primary", $parameters);
    }

    /**
     * Retrieves a collection of Balances from Mollie.
     *
     * @param string|null $from The first Balance ID you want to include in your list.
     * @param int|null $limit
     * @param array $parameters
     *
     * @return BalanceCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): BalanceCollection
    {
        /** @var BalanceCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over balances retrieved from Mollie.
     *
     * @param string $from The first Balance ID you want to include in your list.
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
