<?php

namespace Mollie\Api\Rules;

use Closure;
use Mollie\Api\Contracts\Rule;

class Matches implements Rule
{
    public function __construct(
        private string $pattern
    ) {}

    public static function pattern(string $pattern): self
    {
        return new self($pattern);
    }

    public function validate($value, $context, Closure $fail): void
    {
        if (! preg_match($this->pattern, $value)) {
            $fail("The value {$value} does not match the pattern: {$this->pattern}");
        }
    }
}
