<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum PaymentTerm: string
{
    case Days7 = '7 days';
    case Days14 = '14 days';
    case Days30 = '30 days';
    case Days45 = '45 days';
    case Days60 = '60 days';
    case Days90 = '90 days';
    case Days120 = '120 days';
}
