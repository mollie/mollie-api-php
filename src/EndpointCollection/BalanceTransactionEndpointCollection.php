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
    public function pageFor(Balance $balance, $query = [], bool $testmode = false): BalanceTransactionCollection
    {
        return $this->pageForId($balance->id, $query, $testmode);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance retrieved from Mollie.
     *
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(Balance $balance, array $parameters = [], bool $iterateBackwards = false, bool $testmode = false): LazyCollection
    {
        return $this->iteratorForId($balance->id, $parameters, $iterateBackwards, $testmode);
    }

    /**
     * List the transactions for the primary Balance.
     *
     * @param  array|PaginatedQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForPrimary($query = [], bool $testmode = false): BalanceTransactionCollection
    {
        /** @var BalanceTransactionCollection */
        return $this->pageForId('primary', $query, $testmode);
    }

    /**
     * Create an iterator for iterating over transactions for the primary balance retrieved from Mollie.
     *
     * @param  array|PaginatedQuery  $query
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForPrimary($query = [], bool $iterateBackwards = false, ?bool $testmode = null): LazyCollection
    {
        return $this->iteratorForId('primary', $query, $iterateBackwards);
    }

    /**
     * List the transactions for a specific Balance ID.
     *
     * @param  array|PaginatedQuery  $query
     * @param  bool|null  $testmode
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $balanceId, $query = [], bool $testmode = false): BalanceTransactionCollection
    {
        if (! $query instanceof PaginatedQuery) {
            $query = PaginatedQueryFactory::new($query)
                ->create();
        }

        /** @var BalanceTransactionCollection */
        return $this->send((new GetPaginatedBalanceTransactionRequest($balanceId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance id retrieved from Mollie.
     *
     * @param  array|PaginatedQuery  $query
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(string $balanceId, $query = [], bool $iterateBackwards = false, bool $testmode = false): LazyCollection
    {
        if (! $query instanceof PaginatedQuery) {
            $query = PaginatedQueryFactory::new($query)
                ->create();
        }

        return $this->send(
            (new GetPaginatedBalanceTransactionRequest($balanceId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
