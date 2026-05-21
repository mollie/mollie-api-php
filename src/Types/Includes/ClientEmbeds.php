<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

/**
 * @method static self organization()
 * @method static self onboarding()
 */
final class ClientEmbeds extends QueryParameterSet
{
    protected static function options(): array
    {
        return [
            'organization' => ClientEmbed::Organization,
            'onboarding' => ClientEmbed::Onboarding,
        ];
    }
}
