<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Traits\IsIteratableRequest;

/**
 * @extends PaginatedRequest<\Mollie\Api\Resources\ProfileCollection>
 */
class GetPaginatedProfilesRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = ProfileCollection::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'profiles';
    }
}
