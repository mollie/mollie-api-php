<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Resources\LazyCollection;

class BalanceTransactionEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "balances_transactions";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = BalanceTransaction::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = BalanceTransactionCollection::class;

    /**
     * List the transactions for a specific Balance.
     *
     * @param Balance $balance
     * @param array $parameters
     * @return BalanceTransactionCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Balance $balance, array $parameters = []): BalanceTransactionCollection
    {
        return $this->listForId($balance->id, $parameters);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance retrieved from Mollie.
     *
     * @param Balance $balance
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorFor(Balance $balance, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->iteratorForId($balance->id, $parameters, $iterateBackwards);
    }

    /**
     * List the transactions for a specific Balance ID.
     *
     * @param string $balanceId
     * @param array $parameters
     * @return BalanceTransactionCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $balanceId, array $parameters = []): BalanceTransactionCollection
    {
        $this->parentId = $balanceId;

        /** @var BalanceTransactionCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance id retrieved from Mollie.
     *
     * @param string $balanceId
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(string $balanceId, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        $this->parentId = $balanceId;

        return $this->createIterator(null, null, $parameters, $iterateBackwards);
    }

    /**
     * List the transactions for the primary Balance.
     *
     * @param array $parameters
     * @return BalanceTransactionCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForPrimary(array $parameters = []): BalanceTransactionCollection
    {
        $this->parentId = "primary";

        /** @var BalanceTransactionCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over transactions for the primary balance retrieved from Mollie.
     *
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForPrimary(array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        $this->parentId = "primary";

        return $this->createIterator(null, null, $parameters, $iterateBackwards);
    }
}
