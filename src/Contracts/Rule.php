<?php

namespace Mollie\Api\Contracts;

use Closure;

interface Rule
{
    public function validate($value, Closure $fail, $context): void;
}
