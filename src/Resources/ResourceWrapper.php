<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Traits\ForwardsCalls;

abstract class ResourceWrapper implements IsWrapper
{
    use ForwardsCalls;

    protected $resource;

    public function setResource($resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function __get($name)
    {
        return $this->resource->{$name};
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->resource, $name, $arguments);
    }
}
