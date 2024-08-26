<?php

namespace Mollie\Api\Http\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaginatedBalancesRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;

class BalanceEndpointCollection extends Endpoint
{
    /**
     * Get the balance endpoint.
     *
     * @return BalanceCollection
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): BalanceCollection
    {
        return $this->send(new GetPaginatedBalancesRequest(
            filters: $parameters,
            from: $from,
            limit: $limit
        ));
    }

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
}
