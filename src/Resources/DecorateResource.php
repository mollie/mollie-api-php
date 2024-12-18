<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\ResourceDecorator;

class DecorateResource
{
    protected string $decoratedResource;

    protected ?string $decorator = null;

    public function __construct(string $decoratedResource, ?string $decorator = null)
    {
        $this->decoratedResource = $decoratedResource;

        if ($decorator) {
            $this->with($decorator);
        }
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
        if (! $this->decorator) {
            throw new \InvalidArgumentException('The decorator class is not set.');
        }

        return $this->decorator;
    }
}
