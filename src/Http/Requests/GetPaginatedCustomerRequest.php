<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedCustomerRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected $hydratableResource = CustomerCollection::class;

    public function resolveResourcePath(): string
    {
        return 'customers';
    }
}
