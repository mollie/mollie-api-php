<?php

namespace Mollie\Api\Resources;

class PaymentCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'payments';

    /**
     * Resource class name.
     */
    public static string $resource = Payment::class;
}
