<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Utils\Utility;

abstract class RequestFactory extends Factory
{
    private array $payload = [];

    private array $query = [];

    /**
     * @return static
     */
    public function withPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return static
     */
    public function withQuery(array $query)
    {
        $this->query = $query;

        return $this;
    }

    protected function payload(?string $key = null, $default = null)
    {
        return $this->get($key, $default, $this->payload);
    }

    protected function query(?string $key = null, $default = null)
    {
        return $this->get($key, $default, $this->query);
    }

    protected function payloadIncludes(string $key, $value)
    {
        return $this->includes($key, $value, $this->payload);
    }

    protected function queryIncludes(string $key, $value)
    {
        return $this->includes($key, $value, $this->query);
    }

    protected function payloadHas($key): bool
    {
        return $this->has($key, $this->payload);
    }

    /**
     * @param  string|array<string>  $key
     */
    protected function queryHas($key): bool
    {
        return $this->has($key, $this->query);
    }

    protected function transformFromPayload($key, $resolver, $composableClass = null)
    {
        return $this->transformFromResolved($this->payload($key), $resolver, $composableClass);
    }

    protected function transformFromQuery($key, $resolver, $composableClass = null)
    {
        return $this->transformFromResolved($this->query($key), $resolver, $composableClass);
    }

    /**
     * Map a value to a new form if it is not null.
     */
    protected function transformFromResolved($resolvedValue, $composable, $resolver = null)
    {
        return Utility::transform($resolvedValue, $composable, $resolver);
    }
}
