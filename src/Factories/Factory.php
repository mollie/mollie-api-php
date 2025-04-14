<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

abstract class Factory
{
    private array $data;

    public function __construct($data = null)
    {
        if ($data instanceof Arrayable) {
            $this->data = $data->toArray();
        } else {
            $this->data = $data ?: [];
        }
    }

    /**
     * @return static
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }

    /**
     * Get a value from the data array or the backup key.
     *
     * @param  string|array<string>  $key
     * @param  mixed  $default
     */
    protected function get($key = null, $default = null, $data = null, $backupKey = 'filters.')
    {
        $data = $data ?? $this->data;

        $keys = (array) $key;

        if (empty($keys)) {
            return $data;
        }

        if ($backupKey !== null) {
            $keys[] = $backupKey.$key;
        }

        foreach ($keys as $key) {
            if ($value = Arr::get($data, $key, $default)) {
                return $value;
            }
        }

        return $default;
    }

    protected function has($keys, $data = null): bool
    {
        return Arr::has($data ?? $this->data, $keys);
    }

    /**
     * @param  string|array<string>  $key
     * @param  mixed  $value
     */
    protected function includes($key, $value, $data = null, $backupKey = 'filters.'): bool
    {
        return Arr::includes($data ?? $this->data, [$backupKey.$key, $key], $value);
    }

    /**
     * Map a value to a new form if it is not null.
     *
     * @param  string|array<string>  $key  The key to retrieve the value from the data array.
     * @param  callable|string  $resolver  A callable function to transform the value, or the name of a class to instantiate.
     * @param  string  $composableClass  The class to instantiate if the resolver is a string.
     * @return mixed The transformed value, a new class instance, or null if the value is null.
     */
    protected function transformIfNotNull($key, $resolver, $composableClass = null)
    {
        return Utility::transform($this->get($key), $resolver, $composableClass);
    }
}
