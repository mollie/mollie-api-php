<?php

namespace Mollie\Api\Resources;

class PermissionCollection extends BaseCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "permissions";
    }
}
