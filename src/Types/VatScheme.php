<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum VatScheme: string
{
    case Standard = 'standard';
    case OneStopShop = 'one-stop-shop';
}
