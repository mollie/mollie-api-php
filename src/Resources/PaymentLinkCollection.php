<?php

namespace Mollie\Api\Resources;

class PaymentLinkCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "payment_links";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return PaymentLink::class;
    }
}
