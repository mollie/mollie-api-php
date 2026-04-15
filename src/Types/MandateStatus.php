<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum MandateStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case Invalid = 'invalid';
}
