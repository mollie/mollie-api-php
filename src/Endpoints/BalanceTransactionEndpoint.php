<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Generator;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BalanceTransactionCollection;

class BalanceTransactionEndpoint extends CollectionEndpointAbstract
{
    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'baltr_';

    /**
     * @var string
     */
    protected $resourcePath = "balances_transactions";

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new BalanceTransactionCollection($this->client, $count, $_links);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceObject()
    {
        return new BalanceTransaction($this->client);
    }

    /**
     * List the transactions for a specific Balance.
     *
     * @param Balance $balance
     * @param array $parameters
     * @return BalanceTransactionCollection|\Mollie\Api\Resources\BaseCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Balance $balance, array $parameters = [])
    {
        return $this->listForId($balance->id, $parameters);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance retrieved from Mollie.
     *
     * @param Balance $balance
     * @param array $parameters
     * @param boolean $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return Generator
     */
    public function iteratorFor(Balance $balance, array $parameters = [], bool $iterateBackwards = false): Generator
    {
        return $this->iteratorForId($balance->id, $parameters, $iterateBackwards);
    }

    /**
     * List the transactions for a specific Balance ID.
     *
     * @param string $balanceId
     * @param array $parameters
     * @return BalanceTransactionCollection|\Mollie\Api\Resources\BaseCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $balanceId, array $parameters = [])
    {
        $this->parentId = $balanceId;

        return parent::rest_list(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over balance transactions for the given balance id retrieved from Mollie.
     *
     * @param string $balanceId
     * @param array $parameters
     * @param boolean $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return Generator
     */
    public function iteratorForId(string $balanceId, array $parameters = [], bool $iterateBackwards = false): Generator
    {
        $this->parentId = $balanceId;

        return $this->rest_iterator(null, null, $parameters, $iterateBackwards);
    }

    /**
     * List the transactions for the primary Balance.
     *
     * @param array $parameters
     * @return BalanceTransactionCollection|\Mollie\Api\Resources\BaseCollection
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForPrimary(array $parameters = [])
    {
        $this->parentId = "primary";

        return parent::rest_list(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over transactions for the primary balance retrieved from Mollie.
     *
     * @param array $parameters
     * @param boolean $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return Generator
     */
    public function iteratorForPrimary(array $parameters = [], bool $iterateBackwards = false): Generator
    {
        $this->parentId = "primary";

        return $this->rest_iterator(null, null, $parameters, $iterateBackwards);
    }
}
