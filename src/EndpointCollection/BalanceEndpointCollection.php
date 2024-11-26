<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedBalanceQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Resources\LazyCollection;

class BalanceEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single balance from Mollie.
     *
     * Will throw an ApiException if the balance id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function get(string $id, $testmode = []): Balance
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Balance */
        return $this->send((new GetBalanceRequest($id))->test($testmode));
    }

    /**
     * Retrieve the primary balance from Mollie.
     *
     * Will throw an ApiException if the balance id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function primary(array $testmode = []): Balance
    {
        /** @var Balance */
        return $this->get('primary', $testmode);
    }

    /**
     * Get the balance endpoint.
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): BalanceCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = GetPaginatedBalanceQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send((new GetPaginatedBalanceRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over balances retrieved from Mollie.
     *
     * @param  string  $from  The first Balance ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = GetPaginatedBalanceQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedBalanceRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
