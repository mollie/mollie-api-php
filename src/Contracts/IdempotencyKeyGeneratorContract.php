<?php

namespace Mollie\Api\Contracts;

interface IdempotencyKeyGeneratorContract
{
    public function generate(): string;
}
