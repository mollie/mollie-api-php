<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Capability;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Capability>
 */
class GetCapabilityRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Capability::class;

    public function __construct(
        private string $name,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return "capabilities/{$this->name}";
    }
}
