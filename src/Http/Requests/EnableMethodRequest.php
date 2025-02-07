<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Method;
use Mollie\Api\Types\Method as HttpMethod;

class EnableMethodRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Method::class;

    private string $profileId;

    private string $methodId;

    public function __construct(string $profileId, string $methodId)
    {
        $this->profileId = $profileId;
        $this->methodId = $methodId;
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->profileId}/methods/{$this->methodId}";
    }
}
