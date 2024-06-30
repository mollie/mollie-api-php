<?php

namespace Mollie\Api\Resources;

class MethodCollection extends BaseCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "methods";
    }
}
