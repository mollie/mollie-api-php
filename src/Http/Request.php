<?php

namespace Mollie\Api\Http;

use LogicException;
use Mollie\Api\Contracts\ValidatableDataProvider;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;
use Mollie\Api\Traits\HasRules;

abstract class Request implements ValidatableDataProvider
{
    use HasMiddleware;
    use HasRequestProperties;
    use HasRules;

    /**
     * Define the HTTP method.
     */
    protected static string $method;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass;

    public static bool $shouldAutoHydrate = false;

    public static function hydrate(bool $shouldAutoHydrate = true): void
    {
        self::$shouldAutoHydrate = $shouldAutoHydrate;
    }

    /**
     * Get the method of the request.
     */
    public function getMethod(): string
    {
        if (! isset(static::$method)) {
            throw new LogicException('Your request is missing a HTTP method. You must add a method property like [protected Method $method = Method::GET]');
        }

        return static::$method;
    }

    public function getTargetResourceClass(): string
    {
        if (empty(static::$targetResourceClass)) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return static::$targetResourceClass;
    }

    /**
     * Resolve the resource path.
     */
    abstract public function resolveResourcePath(): string;
}
