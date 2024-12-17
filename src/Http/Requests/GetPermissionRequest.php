<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Types\Method;

class GetPermissionRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Permission::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "permissions/{$this->id}";
    }
}
