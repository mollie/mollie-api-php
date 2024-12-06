<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SessionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSessionsRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    public static string $targetResourceClass = SessionCollection::class;

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
