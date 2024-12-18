<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedBalanceRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = BalanceCollection::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'balances';
    }
}
