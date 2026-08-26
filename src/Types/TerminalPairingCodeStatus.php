<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum TerminalPairingCodeStatus: string
{
    /**
     * Valid and ready to use.
     */
    case Active = 'active';

    /**
     * Past its expiry date. Cannot be used to pair new terminals.
     */
    case Expired = 'expired';

    /**
     * Manually revoked. Cannot be used to pair new terminals.
     */
    case Revoked = 'revoked';
}
