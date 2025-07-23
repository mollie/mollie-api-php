<?php

namespace Mollie\Api\Types;

class WebhookStatus
{
    /**
     * The webhook is enabled and will receive events.
     */
    public const ENABLED = 'enabled';

    /**
     * The webhook is blocked and will not receive events.
     */
    public const BLOCKED = 'blocked';

    /**
     * The webhook is disabled and will not receive events.
     */
    public const DISABLED = 'disabled';
}
