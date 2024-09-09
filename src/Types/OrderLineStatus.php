<?php

namespace Mollie\Api\Types;

class OrderLineStatus
{
    /**
     * The order line has just been created.
     */
    public const CREATED = "created";

    /**
     * The order line has been paid.
     */
    public const PAID = "paid";

    /**
     * The order line has been authorized.
     */
    public const AUTHORIZED = "authorized";

    /**
     * The order line has been canceled.
     */
    public const CANCELED = "canceled";

    /**
     * The order line is shipping.
     */
    public const SHIPPING = "shipping";

    /**
     * The order line is completed.
     */
    public const COMPLETED = "completed";
}
