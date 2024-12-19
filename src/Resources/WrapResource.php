<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;

class WrapResource
{
    protected string $wrappedResource;

    protected string $wrapper;

    public function __construct(string $wrappedResource, ?string $wrapper = null)
    {
        $wrapper = $wrapper ?? $wrappedResource;

        if (! is_subclass_of($wrapper, IsWrapper::class)) {
            throw new \InvalidArgumentException("The wrapper class '{$wrapper}' does not implement the IsWrapper interface.");
        }

        $this->wrappedResource = $wrappedResource;
        $this->wrapper = $wrapper;
    }

    public function getWrappedResource(): string
    {
        return $this->wrappedResource;
    }

    public function getWrapper(): ?string
    {
        if (! $this->wrapper) {
            throw new \InvalidArgumentException('The wrapper class is not set.');
        }

        return $this->wrapper;
    }
}
