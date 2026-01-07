<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\PermissionCollection;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/permissions-api/list-permissions
 */
class ListPermissionsRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PermissionCollection::class;

    public function resolveResourcePath(): string
    {
        return 'permissions';
    }
}
