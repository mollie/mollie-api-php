<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;

/**
 * @mixin \Mollie\Api\MollieApiClient
 */
trait HandlesIdempotency
{
    const IDEMPOTENCY_KEY_HEADER = 'Idempotency-Key';

    protected ?IdempotencyKeyGeneratorContract $idempotencyKeyGenerator;

    /**
     * A unique string ensuring a request to a mutating Mollie endpoint is processed only once.
     * This key resets to null after each request.
     */
    protected ?string $idempotencyKey = null;

    /**
     * Set the idempotency key used on the next request. The idempotency key is a unique string ensuring a request to a
     * mutating Mollie endpoint is processed only once. The idempotency key resets to null after each request. Using
     * the setIdempotencyKey method supersedes the IdempotencyKeyGenerator.
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
     * mutating Mollie endpoint is processed only once. Note that the idempotency key gets reset to null after each
     * request.
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
     * Reset the idempotency key. Note that the idempotency key automatically resets to null after each request.
     *
     * @return $this
     */
    public function resetIdempotencyKey(): self
    {
        $this->idempotencyKey = null;

        return $this;
    }

    /**
     * @param  \Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract  $generator
     * @return \Mollie\Api\Contracts\Connector
     */
    public function setIdempotencyKeyGenerator($generator): self
    {
        $this->idempotencyKeyGenerator = $generator;

        return $this;
    }

    /**
     * @return \Mollie\Api\Contracts\Connector
     */
    public function clearIdempotencyKeyGenerator(): self
    {
        $this->idempotencyKeyGenerator = null;

        return $this;
    }
}
