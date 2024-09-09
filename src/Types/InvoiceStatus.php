<?php

namespace Mollie\Api\Types;

class InvoiceStatus
{
    /**
     * The invoice is not paid yet.
     */
    public const OPEN = "open";

    /**
     * The invoice is paid.
     */
    public const PAID = "paid";

    /**
     * Payment of the invoice is overdue.
     */
    public const OVERDUE = "overdue";
}
