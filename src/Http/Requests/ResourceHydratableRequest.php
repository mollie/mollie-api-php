<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;

abstract class ResourceHydratableRequest extends Request
{
    /**
     * The resource class the request should be hydrated into.
     *
     * @var string|null
     */
    protected $hydratableResource = null;

    public function isHydratable(): bool
    {
        return $this->hydratableResource !== null;
    }

    public function getHydratableResource(): string
    {
        if (! $this->isHydratable()) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return $this->hydratableResource;
    }

    public function setHydratableResource(string $hydratableResource): self
    {
        if (! class_exists($hydratableResource)) {
            throw new \InvalidArgumentException("The resource class '{$hydratableResource}' does not exist.");
        }

        /** @phpstan-ignore-next-line */
        if ($this->hydratableResource && ! is_subclass_of($hydratableResource, $this->hydratableResource)) {
            throw new \InvalidArgumentException("The resource class '{$hydratableResource}' does not match the existing resource class '{$this->hydratableResource}'.");
        }

        $this->hydratableResource = $hydratableResource;

        return $this;
    }
}
