<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Contracts\SupportsResourceHydration;

abstract class ResourceHydratableRequest extends Request implements SupportsResourceHydration
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass;

    public function getTargetResourceClass(): string
    {
        if (empty(static::$targetResourceClass)) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return static::$targetResourceClass;
    }
}
