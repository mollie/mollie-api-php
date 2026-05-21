<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

/**
 * @method static self captures()
 * @method static self refunds()
 * @method static self chargebacks()
 */
final class PaymentEmbeds extends QueryParameterSet
{
    protected static function options(): array
    {
        return [
            'captures' => PaymentEmbed::Captures,
            'refunds' => PaymentEmbed::Refunds,
            'chargebacks' => PaymentEmbed::Chargebacks,
        ];
    }
}
