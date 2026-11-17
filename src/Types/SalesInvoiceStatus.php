<?php

namespace Mollie\Api\Types;

class SalesInvoiceStatus
{
    /**
     * The sales invoice is in draft status and has not been sent or paid.
     */
    public const DRAFT = 'draft';

    /**
     * The sales invoice has been issued to the customer but has not been paid yet.
     */
    public const ISSUED = 'issued';

    /**
     * The sales invoice has been fully paid.
     */
    public const PAID = 'paid';

    /**
     * The sales invoice is being issued.
     */
    public const ISSUING = 'issuing';

    /**
     * A payment for the sales invoice is pending.
     */
    public const PENDING_PAYMENT = 'pending-payment';

    /**
     * The sales invoice is past its due date and has not been paid.
     */
    public const OVERDUE = 'overdue';

    /**
     * The payment of a paid sales invoice has been reversed.
     */
    public const PAYMENT_REVERSED = 'payment-reversed';

    /**
     * The sales invoice has been cancelled.
     */
    public const CANCELLED = 'cancelled';

    /**
     * The sales invoice has expired.
     */
    public const EXPIRED = 'expired';

    /**
     * The sales invoice has failed.
     */
    public const FAILED = 'failed';
}
