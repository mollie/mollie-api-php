<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\TerminalCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedTerminalsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = TerminalCollection::class;

    public function resolveResourcePath(): string
    {
        return 'terminals';
    }
}
