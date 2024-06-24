<?php

namespace Mollie\Api\Types;

class SettlementStatus
{
    /**
     * The settlement has not been closed yet.
     */
    public const OPEN = 'open';

    /**
     * The settlement has been closed and is being processed.
     */
    public const PENDING = 'pending';

    /**
     * The settlement has been paid out.
     */
    public const PAIDOUT = 'paidout';

    /**
     * The settlement could not be paid out.
     */
    public const FAILED = 'failed';
}
