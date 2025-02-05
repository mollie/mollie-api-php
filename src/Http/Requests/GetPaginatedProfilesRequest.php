<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedProfilesRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ProfileCollection::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'profiles';
    }
}
