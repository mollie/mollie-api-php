<?php

namespace Mollie\Api\Contracts;

interface IdempotencyContract
{
    public function getIdempotencyKey(): ?string;

    public function getIdempotencyKeyGenerator(): ?IdempotencyKeyGeneratorContract;
}
