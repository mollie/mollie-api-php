<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface Arrayable
{
    public function toArray(): array;
}
