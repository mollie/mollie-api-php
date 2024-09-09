<?php

namespace Mollie\Api\Resources;

class InvoiceCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "invoices";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Invoice::class;
}
