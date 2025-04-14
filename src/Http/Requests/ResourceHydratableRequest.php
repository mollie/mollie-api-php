<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Resources\WrapperResource;

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
     * @var WrapperResource|null
     */
    protected $customHydratableResource = null;

    /**
     * Determine if the request is hydratable.
     */
    public function isHydratable(): bool
    {
        return $this->hydratableResource !== null || $this->customHydratableResource !== null;
    }

    /**
     * @return string|WrapperResource
     */
    public function getHydratableResource()
    {
        if (! $this->isHydratable()) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return $this->customHydratableResource ?? $this->hydratableResource;
    }

    /**
     * @param  string|WrapperResource  $hydratableResource
     */
    public function setHydratableResource($hydratableResource): self
    {
        $this->customHydratableResource = $hydratableResource;

        if (! $this->hydratableResource && $this->customHydratableResource instanceof WrapperResource) {
            $this->hydratableResource = $this->customHydratableResource->getWrapper();
        }

        return $this;
    }

    public function resetHydratableResource(): self
    {
        $this->customHydratableResource = null;

        return $this;
    }
}
