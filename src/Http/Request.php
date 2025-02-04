<?php

namespace Mollie\Api\Http;

use LogicException;
use Mollie\Api\Traits\HandlesDebugging;
use Mollie\Api\Traits\HandlesTestmode;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;

abstract class Request
{
    use HandlesDebugging;
    use HandlesTestmode;
    use HasMiddleware;
    use HasRequestProperties;

    /**
     * Define the HTTP method.
     */
    protected static string $method;

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

    /**
     * Resolve the resource path.
     */
    abstract public function resolveResourcePath(): string;
}
