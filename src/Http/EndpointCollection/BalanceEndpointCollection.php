<?php

namespace Mollie\Api\Http\EndpointCollection;

use Mollie\Api\Http\BaseEndpointCollection;
use Mollie\Api\Http\Requests\GetPaginatedBalancesRequest;
use Mollie\Api\Resources\BalanceCollection;

class BalanceEndpointCollection extends BaseEndpointCollection
{
    /**
     * Get the balance endpoint.
     *
     * @return BalanceEndpoint
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): BalanceCollection
    {
        return $this->send(new GetPaginatedBalancesRequest($from, $limit, $parameters));
    }
}
