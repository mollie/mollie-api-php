<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;

class PermissionEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "permissions";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Permission::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = PermissionCollection::class;

    /**
     * Retrieve a single Permission from Mollie.
     *
     * Will throw an ApiException if the permission id is invalid.
     *
     * @param string $permissionId
     * @param array $parameters
     * @return Permission
     * @throws ApiException
     */
    public function get(string $permissionId, array $parameters = []): Permission
    {
        /** @var Permission */
        return $this->readResource($permissionId, $parameters);
    }

    /**
     * Retrieve all permissions.
     *
     * @param array $parameters
     *
     * @return PermissionCollection
     * @throws ApiException
     */
    public function all(array $parameters = []): PermissionCollection
    {
        /** @var PermissionCollection */
        return $this->fetchCollection(null, null, $parameters);
    }
}
