<?php

namespace Mollie\Api\Resources;

class PermissionCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'permissions';

    public static string $resource = Permission::class;
}
