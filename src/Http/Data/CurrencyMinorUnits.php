<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use InvalidArgumentException;

/**
 * ISO 4217 minor-unit exponents for the currencies Mollie supports.
 *
 * The exponent defines how many decimal places the major currency unit has.
 * Hardcoding /100 everywhere produces wrong values for JPY (0 decimals)
 * and three-decimal currencies like BHD.
 */
final class CurrencyMinorUnits
{
    /**
     * @var array<string, int>
     */
    private const EXPONENTS = [
        'AED' => 2,
        'AUD' => 2,
        'BGN' => 2,
        'BHD' => 3,
        'BRL' => 2,
        'CAD' => 2,
        'CHF' => 2,
        'CZK' => 2,
        'DKK' => 2,
        'EUR' => 2,
        'GBP' => 2,
        'HKD' => 2,
        'HUF' => 2,
        'ILS' => 2,
        'ISK' => 0,
        'JPY' => 0,
        'MXN' => 2,
        'MYR' => 2,
        'NOK' => 2,
        'NZD' => 2,
        'PHP' => 2,
        'PLN' => 2,
        'RON' => 2,
        'RUB' => 2,
        'SEK' => 2,
        'SGD' => 2,
        'THB' => 2,
        'TWD' => 2,
        'USD' => 2,
        'ZAR' => 2,
    ];

    public static function exponent(string $currency): int
    {
        $code = strtoupper($currency);

        if (! isset(self::EXPONENTS[$code])) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported currency "%s"; no minor-unit exponent defined.',
                $currency,
            ));
        }

        return self::EXPONENTS[$code];
    }
}
