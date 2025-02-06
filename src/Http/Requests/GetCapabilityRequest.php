<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Capability;
use Mollie\Api\Types\Method;

class GetCapabilityRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Capability::class;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function resolveResourcePath(): string
    {
        return "capabilities/{$this->name}";
    }
}
