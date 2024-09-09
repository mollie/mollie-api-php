<?php

namespace Mollie\Api\Rules;

use Closure;
use Mollie\Api\Contracts\Rule;
use Mollie\Api\Traits\Makeable;

class Min implements Rule
{
    use Makeable;

    private int $min;

    public function __construct(int $min)
    {
        $this->min = $min;
    }

    public static function value(int $min): self
    {
        return new self($min);
    }

    public function validate($value, $context, Closure $fail): void
    {
        $length = is_numeric($value) ? $value : strlen($value);

        if ($length < $this->min) {
            $fail("The value must be at least {$this->min}.");
        }
    }
}
