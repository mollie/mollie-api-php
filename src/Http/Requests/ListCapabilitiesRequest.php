<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\CapabilityCollection;
use Mollie\Api\Types\Method;

class ListCapabilitiesRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = CapabilityCollection::class;

    public function resolveResourcePath(): string
    {
        return 'capabilities';
    }
}
