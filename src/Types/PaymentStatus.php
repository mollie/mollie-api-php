<?php

namespace Mollie\Api\Types;

class PaymentStatus
{
    /**
     * The payment has just been created, no action has happened on it yet.
     */
    const STATUS_OPEN = "open";

    /**
     * The payment has just been started, no final confirmation yet.
     */
    const STATUS_PENDING = "pending";

    /**
     * The customer has canceled the payment.
     */
    const STATUS_CANCELED = "canceled";

    /**
     * The payment has expired due to inaction of the customer.
     */
    const STATUS_EXPIRED = "expired";

    /**
     * The payment has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The payment has failed.
     */
    const STATUS_FAILED = "failed";
}
