<?php

namespace Mollie\Api\Resources;

class PaymentCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "payments";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Payment::class;
    }
}
