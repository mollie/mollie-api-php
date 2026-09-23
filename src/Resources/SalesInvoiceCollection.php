<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\SalesInvoice>
 */
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
