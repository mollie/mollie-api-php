<?php

namespace Mollie\Api\Resources;

class ShipmentCollection extends BaseCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return 'shipments';
    }
}
