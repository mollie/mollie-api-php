<?php

namespace Mollie\Api\Rules;

use Closure;
use Mollie\Api\Contracts\Rule;
use Mollie\Api\Traits\Makeable;
use ReflectionClass;

/**
 * @method static self make(array $allowed): Included
 */
class Included implements Rule
{
    use Makeable;

    private array $allowed;

    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @param  string|array  $classOrArray
     */
    public static function in($classOrArray): self
    {
        if (is_array($classOrArray)) {
            return new self($classOrArray);
        }

        $reflection = new ReflectionClass($classOrArray);

        return new self($reflection->getConstants());
    }

    public function validate($value, $context, Closure $fail): void
    {
        $values = explode(',', $value);

        foreach ($values as $value) {
            if (! in_array($value, $this->allowed)) {
                $fail("Invalid include: '{$value}'. Allowed are: ".implode(', ', $this->allowed).'.');
            }
        }
    }
}
