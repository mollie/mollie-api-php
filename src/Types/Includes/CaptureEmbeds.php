<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

/**
 * @method static self payment()
 */
final class CaptureEmbeds extends QueryParameterSet
{
    protected static function options(): array
    {
        return [
            'payment' => CaptureEmbed::Payment,
        ];
    }
}
