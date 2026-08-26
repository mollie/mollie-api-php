<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsWrapper;
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
     *
     * Prefer {@see hydrateInto()} and {@see wrapInto()}: this overload keeps the
     * request's original `TResource`, so static analysis cannot see a wrapper
     * or re-targeted class through {@see \Mollie\Api\MollieApiClient::send()}.
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
     * Hydrate into a different canonical resource or collection class.
     *
     * Call request-specific fluent setters before this method: after it, static
     * analysers see the request as `ResourceHydratableRequest<THydrated>`.
     *
     * @template THydrated of BaseResource|ResourceCollection
     *
     * @param  class-string<THydrated>  $target
     *
     * @phpstan-self-out ResourceHydratableRequest<THydrated>
     * @psalm-this-out ResourceHydratableRequest<THydrated>
     *
     * @return $this
     */
    public function hydrateInto(string $target): static
    {
        self::ensureValidHydrationTarget($target);
        $this->hydratableResource = $target;

        return $this;
    }

    /**
     * Decorate the resolved result with a wrapper class implementing {@see IsWrapper}.
     *
     * The wrapper is what `send()` returns, so call this last in a fluent chain.
     *
     * @template TWrapper of IsWrapper
     *
     * @param  class-string<TWrapper>  $wrapper
     *
     * @phpstan-self-out ResourceHydratableRequest<TWrapper>
     * @psalm-this-out ResourceHydratableRequest<TWrapper>
     *
     * @return $this
     */
    public function wrapInto(string $wrapper): static
    {
        $this->resourceWrapper = new WrapperResource($wrapper);

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
