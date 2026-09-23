<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SalesInvoiceStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case Paid = 'paid';
}
