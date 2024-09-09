<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\JsonBodyRepository as JsonBodyRepositoryContract;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class JsonBodyRepository implements JsonBodyRepositoryContract
{
    private array $store = [];

    public function __construct(array $data = [])
    {
        $this->set($data);
    }

    /**
     * @param  mixed  $value
     */
    public function set($value): self
    {
        $this->store = $value;

        return $this;
    }

    public function all(): array
    {
        return $this->store;
    }

    public function remove(string $key): self
    {
        unset($this->store[$key]);

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->store);
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function __toString(): string
    {
        return @json_encode($this->store);
    }

    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface
    {
        return $streamFactory->createStream((string) $this);
    }
}
