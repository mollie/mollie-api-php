<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

/**
 * @method static self issuers()
 * @method static self pricing()
 */
final class MethodIncludes extends QueryParameterSet
{
    protected static function options(): array
    {
        return [
            'issuers' => MethodInclude::Issuers,
            'pricing' => MethodInclude::Pricing,
        ];
    }
}
