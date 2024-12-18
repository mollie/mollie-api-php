<?php

namespace Mollie\Api\Resources;

class PaymentLinkCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'payment_links';

    /**
     * Resource class name.
     */
    public static string $resource = PaymentLink::class;
}
