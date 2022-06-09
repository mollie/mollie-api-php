<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Resources\BaseCollection;

class BalanceEndpoint extends CollectionEndpointAbstract
{
    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'bal_';

    protected $resourcePath = "balances";

    protected function getResourceCollectionObject($count, $_links)
    {
        return new BalanceCollection($this->client, $count, $_links);
    }

    protected function getResourceObject()
    {
        return new Balance($this->client);
    }

    /**
     * Retrieve a single balance from Mollie.
     *
     * Will throw a ApiException if the balance id is invalid or the resource cannot be found.
     *
     * @param string $balanceId
     * @param array $parameters
     * @return Balance
     * @throws ApiException
     */
    public function get($balanceId, array $parameters = [])
    {
        if (empty($balanceId) || strpos($balanceId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid balance ID: '{$balanceId}'. A balance ID should start with '".self::RESOURCE_ID_PREFIX."'.");
        }

        return parent::rest_read($balanceId, $parameters);
    }

    /**
     * Retrieves a collection of Balances from Mollie.
     *
     * @param string $from The first Balance ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return BaseCollection|BalanceCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
