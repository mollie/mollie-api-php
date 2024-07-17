<?php

namespace Mollie\Api;

trait InteractsWithResource
{
    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = '';

    public static function getResourceClass(): string
    {
        if (empty(static::$resource)) {
            throw new \RuntimeException('Resource class name is not set.');
        }

        return static::$resource;
    }
}
