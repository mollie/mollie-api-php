<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsResourceHydration;
use Mollie\Api\Http\Request;

abstract class ResourceHydratableRequest extends Request implements SupportsResourceHydration
{
    /**
     * Whether the request should be automatically hydrated.
     */
    protected static bool $shouldAutoHydrate = false;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass;

    public static function hydrate(bool $shouldAutoHydrate = true): void
    {
        self::$shouldAutoHydrate = $shouldAutoHydrate;
    }

    public function shouldAutoHydrate(): bool
    {
        return self::$shouldAutoHydrate;
    }

    public function getTargetResourceClass(): string
    {
        if (empty(static::$targetResourceClass)) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return static::$targetResourceClass;
    }
}
