<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

abstract class Factory
{
    protected array $payload = [];

    protected array $query = [];

    public function withPayload(array $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    public function withQuery(array $query): static
    {
        $this->query = $query;

        return $this;
    }

    protected function payload(string $key, $default = null)
    {
        return $this->get($this->payload, $key, $default);
    }

    protected function query(string $key, $default = null)
    {
        return $this->get($this->query, $key, $default);
    }

    /**
     * Get a value from the data array or the backup key.
     *
     * @param  string|array<string>  $key
     * @param  mixed  $default
     */
    protected function get(array $data, $key, $default = null, $backupKey = 'filters.')
    {
        $keys = (array) $key;

        if ($backupKey !== null) {
            $keys[] = $backupKey . $key;
        }

        foreach ($keys as $key) {
            if ($value = Arr::get($data, $key, $default)) {
                return $value;
            }
        }

        return $default;
    }

    protected function payloadIncludes(string $key, $value)
    {
        return $this->includes($this->payload, $key, $value);
    }

    protected function queryIncludes(string $key, $value)
    {
        return $this->includes($this->query, $key, $value);
    }

    /**
     * @param  string|array<string>  $key
     * @param  mixed  $value
     */
    protected function includes(array $data, $key, $value, $backupKey = 'filters.'): bool
    {
        return Arr::includes($data, [$backupKey . $key, $key], $value);
    }

    protected function has(array $data, $keys): bool
    {
        return Arr::has($data, $keys);
    }

    /**
     * Map a value to a new form if it is not null.
     *
     * @param  string|array<string>  $key  The key to retrieve the value from the data array.
     * @param  callable|string  $composable  A callable function to transform the value, or the name of a class to instantiate.
     * @param  string  $backupKey  The key to retrieve the value from the data array if the first key is null.
     * @return mixed The transformed value, a new class instance, or null if the value is null.
     */
    protected function mapIfNotNull($key, $composable, $backupKey = 'filters.')
    {
        return Utility::compose($this->get($this->payload, $key, null, $backupKey), $composable);
    }
}
