<?php

declare(strict_types=1);

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Utils\Utility;

class BalanceTransactionEndpointCollection extends EndpointCollection
{
    /**
     * List the transactions for a specific Balance.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function pageFor(Balance $balance, array $query = [], bool $testmode = false): BalanceTransactionCollection
    {
        return $this->pageForId($balance->id, $query, $testmode);
    }

    /**
     * List the transactions for the primary Balance.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function pageForPrimary(array $query = [], bool $testmode = false): BalanceTransactionCollection
    {
        /** @var BalanceTransactionCollection */
        return $this->pageForId('primary', $query, $testmode);
    }

    /**
     * List the transactions for a specific Balance ID.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function pageForId(string $balanceId, array $query = [], bool $testmode = false): BalanceTransactionCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $query = PaginatedQueryFactory::new()
            ->withQuery($query)
            ->create();

        /** @var BalanceTransactionCollection */
        return $this->send((new GetPaginatedBalanceTransactionRequest(
            $balanceId,
            $query->from,
            $query->limit,
        ))->test($testmode));
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance retrieved from Mollie.
     *
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(Balance $balance, array $query = [], bool $iterateBackwards = false, bool $testmode = false): LazyCollection
    {
        return $this->iteratorForId($balance->id, $query, $iterateBackwards, $testmode);
    }

    /**
     * Create an iterator for iterating over transactions for the primary balance retrieved from Mollie.
     *
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForPrimary(array $query = [], bool $iterateBackwards = false, ?bool $testmode = null): LazyCollection
    {
        return $this->iteratorForId('primary', $query, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance id retrieved from Mollie.
     *
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(string $balanceId, array $query = [], bool $iterateBackwards = false, bool $testmode = false): LazyCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $query = PaginatedQueryFactory::new()
            ->withQuery($query)
            ->create();

        return $this->send(
            (new GetPaginatedBalanceTransactionRequest(
                $balanceId,
                $query->from,
                $query->limit,
            ))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
