<?php

namespace Mollie\Api\Types;

class TerminalPairingCodeStatus
{
    /**
     * Valid and ready to use.
     */
    public const ACTIVE = 'active';

    /**
     * Past its expiry date. Cannot be used to pair new terminals.
     */
    public const EXPIRED = 'expired';

    /**
     * Manually revoked. Cannot be used to pair new terminals.
     */
    public const REVOKED = 'revoked';
}
