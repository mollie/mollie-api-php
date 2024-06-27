<?php

namespace Mollie\Api\Types;

class OrderStatus
{
    /**
     * The order has just been created.
     */
    public const CREATED = "created";

    /**
     * The order has been paid.
     */
    public const PAID = "paid";

    /**
     * The order has been authorized.
     */
    public const AUTHORIZED = "authorized";

    /**
     * The order has been canceled.
     */
    public const CANCELED = "canceled";

    /**
     * The order is shipping.
     */
    public const SHIPPING = "shipping";

    /**
     * The order is completed.
     */
    public const COMPLETED = "completed";

    /**
     * The order is expired.
     */
    public const EXPIRED = "expired";

    /**
     * The order is pending.
     */
    public const PENDING = "pending";
}
