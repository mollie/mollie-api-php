<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Permission;

class GetPermissionRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Permission::class;

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
