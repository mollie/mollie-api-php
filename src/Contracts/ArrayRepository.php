<?php

namespace Mollie\Api\Contracts;

interface ArrayRepository
{
    public function set(array $data): static;

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function add(string $key, $value): static;

    public function merge(array ...$data): static;

    public function remove(string $key): static;

    public function all(): array;

    public function isEmpty(): bool;
}
