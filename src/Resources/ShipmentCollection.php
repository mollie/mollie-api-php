<?php

namespace Mollie\Api\Resources;

class ShipmentCollection extends BaseCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return 'shipments';
    }
}
