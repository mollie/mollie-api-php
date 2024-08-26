<?php

namespace Mollie\Api\Contracts;

interface ValidatableData
{
    public function data(): DataToValidate;
}
