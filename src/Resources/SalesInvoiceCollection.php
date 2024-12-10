<?php

namespace Mollie\Api\Resources;

class SalesInvoiceCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'sales_invoices';

    /**
     * Resource class name.
     */
    public static string $resource = SalesInvoice::class;
}
