<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\WrapperResource;

/**
 * @template TResource of object
 */
abstract class ResourceHydratableRequest extends Request
{
    /**
     * The canonical resource class the request should be hydrated into.
     *
     * @var class-string<BaseResource|ResourceCollection>|null
     */
    protected ?string $hydratableResource = null;

    protected ?WrapperResource $resourceWrapper = null;

    /**
     * Determine if the request is hydratable.
     */
    public function isHydratable(): bool
    {
        return $this->hydratableResource !== null || $this->resourceWrapper !== null;
    }

    /**
     * @return string|WrapperResource
     */
    public function getHydratableResource()
    {
        if (! $this->isHydratable()) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return $this->resourceWrapper ?? $this->hydratableResource;
    }

    /**
     * String targets are validated before becoming the canonical target.
     */
    public function setHydratableResource(string|WrapperResource $hydratableResource): self
    {
        if ($hydratableResource instanceof WrapperResource) {
            $this->resourceWrapper = $hydratableResource;

            return $this;
        }

        self::ensureValidHydrationTarget($hydratableResource);
        $this->hydratableResource = $hydratableResource;

        return $this;
    }

    /**
     * @internal Used by the resource resolver during response hydration.
     *
     * @return class-string<BaseResource|ResourceCollection>|null
     */
    public function getHydratableResourceTarget(): ?string
    {
        return $this->hydratableResource;
    }

    /**
     * @internal Used by the resource resolver during response hydration.
     */
    public function getHydratableResourceWrapper(): ?WrapperResource
    {
        return $this->resourceWrapper;
    }

    /**
     * @deprecated This compatibility alias clears only wrapper decoration.
     */
    public function resetHydratableResource(): self
    {
        $this->resourceWrapper = null;

        return $this;
    }

    /**
     * @phpstan-assert class-string<BaseResource|ResourceCollection> $target
     */
    private static function ensureValidHydrationTarget(string $target): void
    {
        if (! is_subclass_of($target, BaseResource::class) && ! is_subclass_of($target, ResourceCollection::class)) {
            throw new \InvalidArgumentException(
                "Hydratable resource class '{$target}' must extend ".BaseResource::class.' or '.ResourceCollection::class.'.'
            );
        }
    }
}
