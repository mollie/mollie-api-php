<?php

namespace Mollie\Api\Types;

class OrderLineStatus
{
    /**
     * The order line has just been created.
     */
    const STATUS_CREATED = "created";

    /**
     * The order line has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The order line has been authorized.
     */
    const STATUS_AUTHORIZED = "authorized";

    /**
     * The order line has been canceled.
     */
    const STATUS_CANCELED = "canceled";

    /**
     * The order line has been refunded.
     */
    const STATUS_REFUNDED = "refunded";

    /**
     * The order line is shipping.
     */
    const STATUS_SHIPPED = "shipped";

    /**
     * The order line is void.
     */
    const STATUS_VOID = "void";
}
