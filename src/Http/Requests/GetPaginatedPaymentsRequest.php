<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedPaymentsRequest extends SortablePaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PaymentCollection::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payments';
    }
}
