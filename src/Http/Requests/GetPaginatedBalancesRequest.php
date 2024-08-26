<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\BalanceCollection;

class GetPaginatedBalancesRequest extends PaginatedRequest
{
    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass = BalanceCollection::class;

    /**
     * Resolve the resource path.
     *
     * @return string
     */
    public function resolveResourcePath(): string
    {
        return "balances";
    }
}
