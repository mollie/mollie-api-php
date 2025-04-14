<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\Repository;
use Mollie\Api\Utils\Arr;

class ArrayStore implements Repository
{
    protected array $store = [];

    public function __construct(array $data = [])
    {
        $this->set($data);
    }

    public function set($data): self
    {
        $this->store = $data;

        return $this;
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->store, $key, $default);
    }

    public function add(string $key, $value): self
    {
        $this->store[$key] = $value;

        return $this;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->store, $key);
    }

    public function merge(array ...$data): self
    {
        $this->store = array_merge($this->store, ...$data);

        return $this;
    }

    public function remove(string $key): self
    {
        Arr::forget($this->store, $key);

        return $this;
    }

    public function all(): array
    {
        return $this->store;
    }

    public function isEmpty(): bool
    {
        return empty($this->store);
    }

    public function isNotEmpty(): bool
    {
        return ! empty($this->store);
    }
}
