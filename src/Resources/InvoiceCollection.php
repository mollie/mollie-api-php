<?php

namespace Mollie\Api\Resources;

class InvoiceCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "invoices";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Invoice::class;
    }
}
