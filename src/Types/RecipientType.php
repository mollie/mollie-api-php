<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum RecipientType: string
{
    case Consumer = 'consumer';
    case Business = 'business';
}
