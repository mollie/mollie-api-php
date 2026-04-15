<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\Concerns\Macroable;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Traits\HasCurrencyConvenienceMethods;

readonly class Money implements Arrayable
{
    use ComposableFromArray;
    use HasCurrencyConvenienceMethods;
    use Macroable;

    public function __construct(
        public string $currency,
        public string $value,
    ) {}

    /**
     * Create a Money instance from an integer amount in the currency's
     * minor unit (e.g. cents for EUR, yen for JPY, fils for BHD).
     *
     * Negative amounts are allowed (refunds). Float input is intentionally
     * unsupported — binary floats cannot represent decimal money values
     * exactly. Callers with a float must format it to a string themselves.
     */
    public static function fromMinorUnits(string $currency, int $amount): self
    {
        $exponent = CurrencyMinorUnits::exponent($currency);
        $negative = $amount < 0;
        $absolute = (string) abs($amount);

        if ($exponent === 0) {
            $value = $absolute;
        } else {
            $padded = str_pad($absolute, $exponent + 1, '0', STR_PAD_LEFT);
            $value = substr($padded, 0, -$exponent).'.'.substr($padded, -$exponent);
        }

        return new self(
            currency: strtoupper($currency),
            value: $negative ? '-'.$value : $value,
        );
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'value' => $this->value,
        ];
    }
}
