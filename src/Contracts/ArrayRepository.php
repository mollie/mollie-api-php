<?php

namespace Mollie\Api\Contracts;

interface ArrayRepository
{
    public function set(array $data): self;

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function add(string $key, $value): self;

    public function merge(array ...$data): self;

    public function remove(string $key): self;

    public function all(): array;

    public function isEmpty(): bool;
}
