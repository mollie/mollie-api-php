<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\TerminalCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class GetPaginatedTerminalsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = TerminalCollection::class;

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'terminals';
    }
}
