<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface IdempotencyKeyGeneratorContract
{
    public function generate(): string;
}
