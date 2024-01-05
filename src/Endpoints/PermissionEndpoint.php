<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;

class PermissionEndpoint extends CollectionRestEndpoint
{
    protected string $resourcePath = "permissions";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Permission
    {
        return new Permission($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): PermissionCollection
    {
        return new PermissionCollection($count, $_links);
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
        return $this->rest_read($permissionId, $parameters);
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
        return parent::rest_list(null, null, $parameters);
    }
}
