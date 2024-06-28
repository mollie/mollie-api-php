<?php

namespace Mollie\Api\Resources;

class RefundCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "refunds";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Refund::class;
    }
}
