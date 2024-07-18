<?php

namespace Mollie\Api\Types;

class PaymentStatus
{
    /**
     * The payment has just been created, no action has happened on it yet.
     */
    public const OPEN = "open";

    /**
     * The payment has just been started, no final confirmation yet.
     */
    public const PENDING = "pending";

    /**
     * The payment is authorized, but captures still need to be created in order to receive the money.
     *
     * This is currently only possible for Klarna Pay later and Klarna Slice it. Payments with these payment methods can
     * only be created with the Orders API. You should create a Shipment to trigger the capture to receive the money.
     *
     * @see https://docs.mollie.com/reference/v2/shipments-api/create-shipment
     */
    public const AUTHORIZED = "authorized";

    /**
     * The customer has canceled the payment.
     */
    public const CANCELED = "canceled";

    /**
     * The payment has expired due to inaction of the customer.
     */
    public const EXPIRED = "expired";

    /**
     * The payment has been paid.
     */
    public const PAID = "paid";

    /**
     * The payment has failed.
     */
    public const FAILED = "failed";
}
