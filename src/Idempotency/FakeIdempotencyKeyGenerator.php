<?php

declare(strict_types=1);

namespace Mollie\Api\Idempotency;

use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;

class FakeIdempotencyKeyGenerator implements IdempotencyKeyGeneratorContract
{
    private string $fakeKey;

    public function setFakeKey($fakeKey): void
    {
        $this->fakeKey = $fakeKey;
    }

    public function generate(): string
    {
        return $this->fakeKey;
    }
}
