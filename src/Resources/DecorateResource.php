<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;

class WrapResource
{
    protected string $wrappedResource;

    protected ?string $decorator = null;

    public function __construct(string $decoratedResource, ?string $decorator = null)
    {
        $this->wrappedResource = $decoratedResource;

        if ($decorator) {
            $this->with($decorator);
        }
    }

    public function with(string $decorator): self
    {
        if (! is_subclass_of($decorator, IsWrapper::class)) {
            throw new \InvalidArgumentException("The decorator class '{$decorator}' does not implement the DecoratedResource interface.");
        }

        $this->decorator = $decorator;

        return $this;
    }

    public function getWrappedResource(): string
    {
        return $this->wrappedResource;
    }

    public function getWrapper(): ?string
    {
        if (! $this->decorator) {
            throw new \InvalidArgumentException('The decorator class is not set.');
        }

        return $this->decorator;
    }
}
