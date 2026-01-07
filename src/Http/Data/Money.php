<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Utils\Str;

class Money implements Arrayable
{
    use ComposableFromArray;

    public string $currency;

    public string $value;

    public function __construct(
        string $currency,
        string $value
    ) {
        $this->currency = $currency;
        $this->value = $value;
    }

    /**
     * Create a Money object for EUR currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return self
     */
    public static function euro(string $value): self
    {
        return new self('EUR', $value);
    }

    /**
     * Create a Money object for USD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return self
     */
    public static function usd(string $value): self
    {
        return new self('USD', $value);
    }

    /**
     * Create a Money object from a string representation.
     * Supports formats like "EUR 10.00", "10.00 EUR", "USD 5.50", "5.50 USD".
     *
     * @param string $subject The string to parse (e.g., "EUR 10.00" or "10.00 EUR")
     * @return self
     * @throws \InvalidArgumentException If the string format is invalid
     */
    public static function fromString(string $subject): self
    {
        $subject = trim($subject);

        // Try currency first pattern: "EUR 10.00"
        if ($matches = Str::match($subject, '/^([A-Z]{3})\s+([\d.]+)$/i')) {
            return new self(strtoupper($matches[1]), $matches[2]);
        }

        // Try currency last pattern: "10.00 EUR"
        if ($matches = Str::match($subject, '/^([\d.]+)\s+([A-Z]{3})$/i')) {
            return new self(strtoupper($matches[2]), $matches[1]);
        }


        throw new \InvalidArgumentException(
            "Invalid money string format: '{$subject}'. Expected format: 'EUR 10.00' or '10.00 EUR'"
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
