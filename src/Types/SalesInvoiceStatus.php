<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

/**
 * Create and update requests only accept `Draft`, `Issued` and `Paid` (update also
 * accepts `Cancelled` and `PaymentReversed`). The other cases are response-only:
 * Mollie sets them, so do not send them in a request payload.
 */
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
