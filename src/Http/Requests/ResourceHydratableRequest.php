<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Resources\DecorateResource;

abstract class ResourceHydratableRequest extends Request
{
    /**
     * The original resource class the request should be hydrated into.
     *
     * @var string|null
     */
    protected $hydratableResource = null;

    /**
     * The custom resource class the request should be hydrated into.
     *
     * @var string|null|DecorateResource
     */
    protected $customHydratableResource = null;

    public function isHydratable(): bool
    {
        return $this->hydratableResource !== null || $this->customHydratableResource !== null;
    }

    /**
     * @return string|DecorateResource
     */
    public function getHydratableResource()
    {
        if (! $this->isHydratable()) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return $this->customHydratableResource ?? $this->hydratableResource;
    }

    /**
     * @param  string|DecorateResource  $hydratableResource
     */
    public function setHydratableResource($hydratableResource): self
    {
        if (! class_exists($hydratableResource)) {
            throw new \InvalidArgumentException("The resource class '{$hydratableResource}' does not exist.");
        }

        $this->customHydratableResource = $hydratableResource;

        return $this;
    }

    public function resetHydratableResource(): self
    {
        $this->customHydratableResource = null;

        return $this;
    }
}
