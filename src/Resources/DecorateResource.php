<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\ResourceDecorator;

class DecorateResource
{
    protected string $decoratedResource;

    protected ?string $decorator = null;

    public function __construct(string $decoratedResource)
    {
        $this->decoratedResource = $decoratedResource;
    }

    public function with(string $decorator): self
    {
        if (! is_subclass_of($decorator, ResourceDecorator::class)) {
            throw new \InvalidArgumentException("The decorator class '{$decorator}' does not implement the DecoratedResource interface.");
        }

        $this->decorator = $decorator;

        return $this;
    }

    public function getDecoratedResource(): string
    {
        return $this->decoratedResource;
    }

    public function getDecorator(): ?string
    {
        return $this->decorator;
    }
}
