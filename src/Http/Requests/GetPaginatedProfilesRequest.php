<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Http\Data\PaginatedQuery;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedProfilesRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ProfileCollection::class;

    public function __construct(
        ?PaginatedQuery $query = null
    ) {
        parent::__construct($query);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'profiles';
    }
}
