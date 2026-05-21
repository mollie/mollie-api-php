<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum TerminalPairingCodeStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Revoked = 'revoked';
}
