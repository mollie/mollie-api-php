<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SessionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSessionsRequest extends SortablePaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected $hydratableResource = SessionCollection::class;

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
