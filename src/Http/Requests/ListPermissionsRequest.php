<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\PermissionCollection;
use Mollie\Api\Types\Method;

class ListPermissionsRequest extends Request
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PermissionCollection::class;

    public function resolveResourcePath(): string
    {
        return 'permissions';
    }
}
