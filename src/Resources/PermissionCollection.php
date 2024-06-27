<?php

namespace Mollie\Api\Resources;

class PermissionCollection extends BaseCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "permissions";
    }
}
