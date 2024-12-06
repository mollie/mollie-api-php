<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetPermissionRequest;
use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;

class PermissionEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single Permission from Mollie.
     *
     * Will throw an ApiException if the permission id is invalid.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function get(string $permissionId, $testmode = []): Permission
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Permission */
        return $this->send((new GetPermissionRequest($permissionId))->test($testmode));
    }

    /**
     * Retrieve all permissions from Mollie.
     *
     * @throws ApiException
     */
    public function list(): PermissionCollection
    {
        /** @var PermissionCollection */
        return $this->send(new ListPermissionsRequest);
    }
}
