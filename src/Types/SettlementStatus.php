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
     * The settlement is being processed.
     */
    public const PROCESSING = 'processing';

    /**
     * The settlement has been sent to the bank and is being processed.
     */
    public const PROCESSING_AT_BANK = 'processing-at-bank';

    /**
     * The settlement has been paid out.
     */
    public const PAIDOUT = 'paidout';

    /**
     * The settlement could not be paid out.
     */
    public const FAILED = 'failed';
}
