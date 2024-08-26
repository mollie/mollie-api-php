<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Requests\Request;

interface Rule
{
    public function validate(Request $request): void;
}
