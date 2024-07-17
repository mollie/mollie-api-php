<?php

namespace Mollie\Api\Resources;

class PaymentLinkCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "payment_links";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = PaymentLink::class;
}
