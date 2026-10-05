<?php

namespace Mollie\Api\Resources;

class SalesInvoiceCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'invoices';

    /**
     * Resource class name.
     */
    public static string $resource = SalesInvoice::class;
}
