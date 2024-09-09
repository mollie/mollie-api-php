<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedChargebacksRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = ChargebackCollection::class;

    public function resolveResourcePath(): string
    {
        return 'chargebacks';
    }
}
