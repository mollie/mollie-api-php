<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Types\Method;

class GetProfileRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Profile::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
