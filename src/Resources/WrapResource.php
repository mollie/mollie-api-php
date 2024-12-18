<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;

class WrapResource
{
    protected string $wrappedResource;

    protected ?string $wrapper = null;

    public function __construct(string $decoratedResource, ?string $wrapper = null)
    {
        $this->wrappedResource = $decoratedResource;

        if ($wrapper) {
            $this->with($wrapper);
        }
    }

    public function with(string $wrapper): self
    {
        if (! is_subclass_of($wrapper, IsWrapper::class)) {
            throw new \InvalidArgumentException("The wrapper class '{$wrapper}' does not implement the IsWrapper interface.");
        }

        $this->wrapper = $wrapper;

        return $this;
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
