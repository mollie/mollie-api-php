<?php

namespace Mollie\Api\Rules;

use Closure;
use Mollie\Api\Contracts\Rule;
use Mollie\Api\Traits\Makeable;

class Max implements Rule
{
    use Makeable;

    private int $max;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    public static function value(int $max): self
    {
        return new self($max);
    }

    public function validate($value, $context, Closure $fail): void
    {
        $length = is_numeric($value) ? $value : strlen($value);

        if ($length > $this->max) {
            $fail("The value must not exceed {$this->max}.");
        }
    }
}
