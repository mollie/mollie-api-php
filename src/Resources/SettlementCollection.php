<?php

namespace Mollie\Api\Resources;

class SettlementCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "settlements";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Settlement::class;
    }
}
