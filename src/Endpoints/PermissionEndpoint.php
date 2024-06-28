<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;

class PermissionEndpoint extends EndpointCollection
{
    protected string $resourcePath = "permissions";

    /**
     * @inheritDoc
     */
    public static function getResourceClass(): string
    {
        return  Permission::class;
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionClass(): string
    {
        return PermissionCollection::class;
    }

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
