<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SequenceType: string
{
    case Oneoff = 'oneoff';
    case First = 'first';
    case Recurring = 'recurring';
}
