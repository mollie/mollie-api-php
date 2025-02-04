<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Types\Method;

class GetCurrentProfileRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = CurrentProfile::class;

    public function resolveResourcePath(): string
    {
        return 'profiles/me';
    }
}
