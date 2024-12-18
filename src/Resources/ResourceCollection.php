<?php

namespace Mollie\Api\Resources;

abstract class ResourceCollection extends BaseCollection
{
    /**
     * Resource class name.
     */
    public static string $resource = '';

    public static function getResourceClass(): string
    {
        if (empty(static::$resource)) {
            throw new \RuntimeException('Resource name not set');
        }

        return static::$resource;
    }
}
