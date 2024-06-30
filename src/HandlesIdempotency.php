<?php

namespace Mollie\Api;

use Mollie\Api\Idempotency\DefaultIdempotencyKeyGenerator;
use Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract;

/**
 * @mixin MollieApiClient
 */
trait HandlesIdempotency
{
    /**
     * A unique string ensuring a request to a mutating Mollie endpoint is processed only once.
     * This key resets to null after each request.
     *
     * @var string|null
     */
    protected ?string $idempotencyKey = null;

    /**
     * @var IdempotencyKeyGeneratorContract|null
     */
    protected ?IdempotencyKeyGeneratorContract $idempotencyKeyGenerator;

    /**
     * @param \Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract $generator
     * @return void
     */
    protected function initializeIdempotencyKeyGenerator($generator): void
    {
        $this->idempotencyKeyGenerator = $generator ? $generator : new DefaultIdempotencyKeyGenerator();
    }

    /**
     * Set the idempotency key used on the next request. The idempotency key is a unique string ensuring a request to a
     * mutating Mollie endpoint is processed only once. The idempotency key resets to null after each request. Using
     * the setIdempotencyKey method supersedes the IdempotencyKeyGenerator.
     *
     * @param $key
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
     *
     * @return string|null
     */
    public function getIdempotencyKey(): ?string
    {
        return $this->idempotencyKey;
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
     * @param \Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract $generator
     * @return \Mollie\Api\MollieApiClient
     */
    public function setIdempotencyKeyGenerator($generator): self
    {
        $this->idempotencyKeyGenerator = $generator;

        return $this;
    }

    /**
     * @return \Mollie\Api\MollieApiClient
     */
    public function clearIdempotencyKeyGenerator(): self
    {
        $this->idempotencyKeyGenerator = null;

        return $this;
    }

    /**
     * Conditionally apply the idempotency key to the request headers
     *
     * @param array $headers
     * @param string $httpMethod
     * @return array
     */
    protected function applyIdempotencyKey(array $headers, string $httpMethod)
    {
        if (! in_array($httpMethod, [MollieApiClient::HTTP_POST, MollieApiClient::HTTP_PATCH, MollieApiClient::HTTP_DELETE])) {
            unset($headers['Idempotency-Key']);

            return $headers;
        }

        if ($this->idempotencyKey) {
            $headers['Idempotency-Key'] = $this->idempotencyKey;

            return $headers;
        }

        if ($this->idempotencyKeyGenerator) {
            $headers['Idempotency-Key'] = $this->idempotencyKeyGenerator->generate();

            return $headers;
        }

        unset($headers['Idempotency-Key']);

        return $headers;
    }
}
