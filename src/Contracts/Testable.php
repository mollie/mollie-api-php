<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface Testable
{
    public function getTestmode(): ?bool;
}
