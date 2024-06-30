<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "chargebacks";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Chargeback::class;
    }
}
