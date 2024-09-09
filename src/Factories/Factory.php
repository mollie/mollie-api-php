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
        return new static($data);
    }

    protected function get(string $key, $default = null): mixed
    {
        return Arr::get($this->data, $key, $default);
    }

    protected function has($keys): bool
    {
        return Arr::has($this->data, $keys);
    }

    /**
     * Map a value to a new form if it is not null.
     *
     * @param  string  $key  The key to retrieve the value from the data array.
     * @param  callable|string  $composable  A callable function to transform the value, or the name of a class to instantiate.
     * @return mixed The transformed value, a new class instance, or null if the value is null.
     */
    protected function mapIfNotNull(string $key, $composable): mixed
    {
        return Helpers::compose($this->get($key), $composable);
    }
}
