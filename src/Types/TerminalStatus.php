<?php

namespace Mollie\Api\Types;

class TerminalStatus
{
    /**
     * The terminal has just been created but not yet active.
     */
    public const PENDING = "pending";

    /**
     * The terminal has been activated and can take payments.
     */
    public const ACTIVE = "active";

    /**
     * The terminal has been deactivated.
     */
    public const INACTIVE = "inactive";
}
