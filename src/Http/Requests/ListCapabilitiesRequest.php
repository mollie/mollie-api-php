<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\CapabilityCollection;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/list-capabilities
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\CapabilityCollection>
 */
class ListCapabilitiesRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = CapabilityCollection::class;

    public function resolveResourcePath(): string
    {
        return 'capabilities';
    }
}
