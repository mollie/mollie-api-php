<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Request;

abstract class ComposableRequestFactory extends RequestFactory
{
    protected RequestFactory $factory;

    abstract public function compose(...$data): Request;

    public function withPayload(array $payload): static
    {
        $this->factory->withPayload($payload);

        return $this;
    }

    public function withQuery(array $query): static
    {
        $this->factory->withQuery($query);

        return $this;
    }
}
