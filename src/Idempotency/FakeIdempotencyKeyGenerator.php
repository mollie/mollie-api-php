<?php

declare(strict_types=1);

namespace Mollie\Api\Idempotency;

class FakeIdempotencyKeyGenerator implements IdempotencyKeyGeneratorContract
{
    /** @var string */
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
