<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SettlementCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSettlementsRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = SettlementCollection::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'settlements';
    }
}
