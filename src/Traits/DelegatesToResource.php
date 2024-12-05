<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceCollection;
use ReflectionProperty;

/**
 * @mixin Response
 *
 * @property null|BaseResource|ResourceCollection $resource
 */
trait DelegatesToResource
{
    /**
     * Determine if an attribute exists on the resource.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        $this->ensureResourceIsLoaded();

        return isset($this->resource->{$key});
    }

    /**
     * Unset an attribute on the resource.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->ensureResourceIsLoaded();

        unset($this->resource->{$key});
    }

    /**
     * Dynamically get properties from the underlying resource.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $this->ensureResourceIsLoaded();

        if (! property_exists($this->resource, $key)) {
            throw new \InvalidArgumentException("Property {$key} does not exist on resource.");
        }

        $reflectionProperty = new ReflectionProperty($this->resource, $key);

        return $reflectionProperty->getValue($this->resource);
    }

    /**
     * Dynamically pass method calls to the underlying resource.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->ensureResourceIsLoaded();

        if (method_exists($this->resource, $method)) {
            return call_user_func_array([$this->resource, $method], $parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on resource.");
    }

    /**
     * Ensure the resource is loaded.
     */
    private function ensureResourceIsLoaded(): void
    {
        if ($this->resource) {
            return;
        }

        $this->toResource();
    }
}
