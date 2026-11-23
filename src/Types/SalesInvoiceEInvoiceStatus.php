<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

/**
 * The e-invoice submission status of a sales invoice. Read-only; Mollie sets it.
 */
enum SalesInvoiceEInvoiceStatus: string
{
    case Issuing = 'issuing';
    case Issued = 'issued';
    case Failed = 'failed';
}
