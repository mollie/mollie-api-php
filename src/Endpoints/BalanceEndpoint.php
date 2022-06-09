<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Resources\BaseCollection;

class BalanceEndpoint extends CollectionEndpointAbstract
{
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
