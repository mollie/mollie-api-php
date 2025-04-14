<?php

namespace Mollie\Api\Types;

class RefundStatus
{
    /**
     * The refund is queued until there is enough balance to process te refund. You can still cancel the refund.
     */
    public const QUEUED = 'queued';

    /**
     * The refund will be sent to the bank on the next business day. You can still cancel the refund.
     */
    public const PENDING = 'pending';

    /**
     * The refund has been sent to the bank. The refund amount will be transferred to the consumer account as soon as possible.
     */
    public const PROCESSING = 'processing';

    /**
     * The refund amount has been transferred to the consumer.
     */
    public const REFUNDED = 'refunded';

    /**
     * The refund has failed after processing. For example, the customer has closed his / her bank account. The funds will be returned to your account.
     */
    public const FAILED = 'failed';

    /**
     * The refund was canceled and will no longer be processed.
     */
    public const CANCELED = 'canceled';
}
