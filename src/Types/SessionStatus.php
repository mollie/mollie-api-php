<?php

namespace Mollie\Api\Types;

class SessionStatus
{
    /**
     * The session is open.
     */
    public const OPEN = 'open';

    /**
     * The session has expired.
     */
    public const EXPIRED = 'expired';

    /**
     * The session is completed.
     */
    public const COMPLETED = 'completed';
}
