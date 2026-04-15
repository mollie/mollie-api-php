<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum InvoiceStatus: string
{
    /**
     * The invoice is not paid yet.
     */
    case Open = 'open';

    /**
     * The invoice is paid.
     */
    case Paid = 'paid';

    /**
     * Payment of the invoice is overdue.
     */
    case Overdue = 'overdue';
}
