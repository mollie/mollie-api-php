<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use Mollie\Api\Types\MethodQuery;

enum MethodInclude: string
{
    case Issuers = MethodQuery::INCLUDE_ISSUERS;
    case Pricing = MethodQuery::INCLUDE_PRICING;
}
