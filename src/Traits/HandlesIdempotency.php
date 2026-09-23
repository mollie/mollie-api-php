<?php

declare(strict_types=1);

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;

/**
 * @mixin \Mollie\Api\MollieApiClient
 */
trait HandlesIdempotency
{
    protected ?IdempotencyKeyGeneratorContract $idempotencyKeyGenerator;

    /**
     * A unique string ensuring a request to a mutating Mollie endpoint is processed only once.
     * This key is consumed when the next request is assembled.
     */
    protected ?string $idempotencyKey = null;

    /**
     * Set the idempotency key used on the next request. The idempotency key is a unique string ensuring a request to a
     * mutating Mollie endpoint is processed only once. The idempotency key is consumed when the next request is
     * assembled. Using the setIdempotencyKey method supersedes the IdempotencyKeyGenerator.
     *
     * @return $this
     */
    public function setIdempotencyKey($key): self
    {
        $this->idempotencyKey = $key;

        return $this;
    }

    /**
     * Retrieve the idempotency key. The idempotency key is a unique string ensuring a request to a
     * mutating Mollie endpoint is processed only once. The key is null after the next request is assembled.
     */
    public function getIdempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public function getIdempotencyKeyGenerator(): ?IdempotencyKeyGeneratorContract
    {
        return $this->idempotencyKeyGenerator;
    }

    /**
     * Reset the idempotency key. The key is also consumed automatically when the next request is assembled.
     *
     * @return $this
     */
    public function resetIdempotencyKey(): self
    {
        $this->idempotencyKey = null;

        return $this;
    }

    /**
     * @return $this
     */
    public function setIdempotencyKeyGenerator(IdempotencyKeyGeneratorContract $generator): self
    {
        $this->idempotencyKeyGenerator = $generator;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearIdempotencyKeyGenerator(): self
    {
        $this->idempotencyKeyGenerator = null;

        return $this;
    }
}
