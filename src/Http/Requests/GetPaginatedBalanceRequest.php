<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedBalanceRequest extends SortablePaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected $hydratableResource = BalanceCollection::class;

    public function resolveResourcePath(): string
    {
        return 'balances';
    }
}
