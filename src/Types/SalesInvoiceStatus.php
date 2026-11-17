<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SalesInvoiceStatus: string
{
    case Draft = 'draft';
    case Issuing = 'issuing';
    case Issued = 'issued';
    case PendingPayment = 'pending-payment';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case PaymentReversed = 'payment-reversed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Failed = 'failed';
}
