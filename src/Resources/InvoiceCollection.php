<?php

namespace Mollie\Api\Resources;

class InvoiceCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "invoices";
    }

    /**
     * @return Invoice
     */
    protected function createResourceObject(): Invoice
    {
        return new Invoice($this->client);
    }
}
