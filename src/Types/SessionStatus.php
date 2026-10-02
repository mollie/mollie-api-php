<?php

namespace Mollie\Api\Types;

class SessionStatus
{
    /**
     * The session is open and can still be completed.
     */
    public const STATUS_OPEN = "open";

    /**
     * The session has just been created.
     *
     * @deprecated Use STATUS_OPEN for Checkout Sessions.
     */
    public const STATUS_CREATED = "created";

    /**
     * The session has been paid.
     */
    public const STATUS_READY_FOR_PROCESSING = "ready_for_processing";

    /**
     * The session is completed.
     */
    public const STATUS_COMPLETED = "completed";

    /**
     * The session expired before it was completed.
     */
    public const STATUS_EXPIRED = "expired";

    /**
     * The session has failed.
     *
     * @deprecated Use STATUS_EXPIRED for Checkout Sessions.
     */
    public const STATUS_FAILED = "failed";
}
