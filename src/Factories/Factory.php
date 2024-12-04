<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Contracts\Factory as FactoryContract;
use Mollie\Api\Helpers;
use Mollie\Api\Helpers\Arr;

abstract class Factory implements FactoryContract
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function new(array $data): self
    {
        /** @phpstan-ignore-next-line */
        return new static($data);
    }

    /**
     * Get a value from the data array or the backup key.
     *
     * @param  string|array<string>  $key
     * @param  mixed  $default
     */
    protected function get($key, $default = null, $backupKey = 'filters.')
    {
        $keys = (array) $key;

        if ($backupKey !== null) {
            $keys[] = $backupKey.$key;
        }

        foreach ($keys as $key) {
            if ($value = Arr::get($this->data, $key, $default)) {
                return $value;
            }
        }

        return $default;
    }

    protected function has($keys): bool
    {
        return Arr::has($this->data, $keys);
    }

    /**
     * @param  string|array<string>  $key
     * @param  mixed  $value
     */
    protected function includes($key, $value, $backupKey = 'filters.'): bool
    {
        return Arr::includes($this->data, [$backupKey.$key, $key], $value);
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
        return Helpers::compose($this->get($key, null, $backupKey), $composable);
    }
}
