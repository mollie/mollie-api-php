<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Utils\Arr;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Stringable;

class JsonPayloadRepository implements PayloadRepository, Stringable
{
    private array $store = [];

    public function __construct(array $data = [])
    {
        $this->set($data);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->store, $key);
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

    public function add(string $key, $value): self
    {
        $this->store[$key] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->store[$key] ?? $default;
    }

    public function merge(array ...$arrays): self
    {
        $this->store = array_merge($this->store, ...$arrays);

        return $this;
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

    public function resolve(): static
    {
        $this->store = Arr::resolve($this->store);

        return $this;
    }
}
