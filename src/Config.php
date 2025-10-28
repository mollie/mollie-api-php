<?php

namespace Mollie\Api;

use Mollie\Api\Resources\ResourceRegistry;

class Config
{
    /**
     * Resolver used to fetch the global ResourceRegistry, e.g. from a container.
     *
     * @var callable():ResourceRegistry|null
     */
    private static $resourceRegistryResolver = null;

    /**
     * Set a resolver that returns a ResourceRegistry instance. Pass null to reset.
     *
     * @param callable():ResourceRegistry|null $resolver
     */
    public static function setResourceRegistryResolver(?callable $resolver): void
    {
        self::$resourceRegistryResolver = $resolver;
    }

    /**
     * Resolve the ResourceRegistry. Uses the resolver if set, otherwise defaults.
     */
    public static function resourceRegistry(): ResourceRegistry
    {
        $resolver = self::$resourceRegistryResolver;

        if (! is_callable($resolver)) {
            return ResourceRegistry::default();
        }

        $registry = call_user_func($resolver);

        if (! $registry instanceof ResourceRegistry) {
            return ResourceRegistry::default();
        }

        return $registry;
    }
}
