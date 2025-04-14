<?php

namespace Mollie\Api\Contracts;

interface Repository
{
    /**
     * Set the entire repository data
     *
     * @param  mixed  $data  Array for array repositories, mixed for payload repositories
     * @return static
     */
    public function set($data);

    /**
     * Get a value by key
     *
     * @param  mixed  $default
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
     * @param  mixed  $value
     * @return static
     */
    public function add(string $key, $value);

    /**
     * Merge data into the repository
     *
     * @return static
     */
    public function merge(array ...$data);

    /**
     * Remove a key from the repository
     */
    public function remove(string $key);

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
}
