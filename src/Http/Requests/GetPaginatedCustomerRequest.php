<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedCustomerRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    public static string $targetResourceClass = CustomerCollection::class;

    public function resolveResourcePath(): string
    {
        return 'customers';
    }
}
