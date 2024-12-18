<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedClientRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ClientCollection::class;

    public function resolveResourcePath(): string
    {
        return 'clients';
    }
}
