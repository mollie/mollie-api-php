<?php

declare(strict_types=1);

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Resources\LazyCollection;

class BalanceTransactionEndpointCollection extends EndpointCollection
{
    /**
     * List the transactions for a specific Balance.
     *
     * @param  array|PaginatedQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Balance $balance, $query = []): BalanceTransactionCollection
    {
        return $this->listForId($balance->id, $query);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance retrieved from Mollie.
     *
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(Balance $balance, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->iteratorForId($balance->id, $parameters, $iterateBackwards);
    }

    /**
     * List the transactions for the primary Balance.
     *
     * @param  array|PaginatedQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForPrimary($query = []): BalanceTransactionCollection
    {
        /** @var BalanceTransactionCollection */
        return $this->listForId('primary', $query);
    }

    /**
     * Create an iterator for iterating over transactions for the primary balance retrieved from Mollie.
     *
     * @param  array|PaginatedQuery  $query
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForPrimary($query = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->iteratorForId('primary', $query, $iterateBackwards);
    }

    /**
     * List the transactions for a specific Balance ID.
     *
     * @param  array|PaginatedQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $balanceId, $query = []): BalanceTransactionCollection
    {
        if (! $query instanceof PaginatedQuery) {
            $query = PaginatedQueryFactory::new($query)
                ->create();
        }

        /** @var BalanceTransactionCollection */
        return $this->send(new GetPaginatedBalanceTransactionRequest($balanceId, $query));
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance id retrieved from Mollie.
     *
     * @param  array|PaginatedQuery  $query
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(string $balanceId, $query = [], bool $iterateBackwards = false): LazyCollection
    {
        if (! $query instanceof PaginatedQuery) {
            $query = PaginatedQueryFactory::new($query)
                ->create();
        }

        return $this->send(
            (new GetPaginatedBalanceTransactionRequest($balanceId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
