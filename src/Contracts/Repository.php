<?php

namespace Mollie\Api\Contracts;

interface Repository
{
    /**
     * Set the entire repository data
     *
     * @param mixed $data Array for array repositories, mixed for payload repositories
     * @return self
     */
    public function set($data): self;

    /**
     * Get a value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Check if a key exists
     */
    public function has(string $key): bool;

    /**
     * Add a value to the repository
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function add(string $key, $value): self;

    /**
     * Merge data into the repository
     *
     * @param array ...$data
     * @return self
     */
    public function merge(array ...$data): self;

    /**
     * Remove a key from the repository
     */
    public function remove(string $key): self;

    /**
     * Get all data from the repository
     *
     * @return mixed
     */
    public function all();

    /**
     * Check if the repository is empty
     */
    public function isEmpty(): bool;

    /**
     * Check if the repository is not empty
     */
    public function isNotEmpty(): bool;

    public function resolve(): static;
}
