<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\ArrayRepository;
use Mollie\Api\Helpers\Arr;

class ArrayStore implements ArrayRepository
{
    private array $store = [];

    public function __construct(array $data)
    {
        $this->store = $data;
    }

    public function set(array $data): static
    {
        $this->store = $data;

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store[$key] ?? $default;
    }

    public function add(string $key, $value): static
    {
        $this->store[$key] = $value;

        return $this;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->store, $key);
    }

    public function merge(array ...$data): static
    {
        $this->store = array_merge($this->store, ...$data);

        return $this;
    }

    public function remove(string $key): static
    {
        unset($this->store[$key]);

        return $this;
    }

    public function all(): array
    {
        return array_filter($this->store, fn ($value) => ! empty($value));
    }

    public function isEmpty(): bool
    {
        return empty($this->store);
    }
}
