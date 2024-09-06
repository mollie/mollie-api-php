<?php

namespace Mollie\Api\Contracts;

interface ValidatableDataProvider
{
    public function rules(): array;
}
