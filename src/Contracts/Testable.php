<?php

namespace Mollie\Api\Contracts;

interface Testable
{
    public function getTestmode(): ?bool;
}
