<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum PaymentStatus: string
{
    /**
     * The payment has just been created, no action has happened on it yet.
     */
    case Open = 'open';

    /**
     * The payment has just been started, no final confirmation yet.
     */
    case Pending = 'pending';

    /**
     * The payment is authorized, but captures still need to be created in order to receive the money.
     *
     * @see https://docs.mollie.com/reference/v2/shipments-api/create-shipment
     */
    case Authorized = 'authorized';

    /**
     * The customer has canceled the payment.
     */
    case Canceled = 'canceled';

    /**
     * The payment has expired due to inaction of the customer.
     */
    case Expired = 'expired';

    /**
     * The payment has been paid.
     */
    case Paid = 'paid';

    /**
     * The payment has failed.
     */
    case Failed = 'failed';
}
