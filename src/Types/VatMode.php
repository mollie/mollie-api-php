<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum VatMode: string
{
    case Exclusive = 'exclusive';
    case Inclusive = 'inclusive';
}
