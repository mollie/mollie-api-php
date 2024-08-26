<?php

namespace Mollie\Api\Http\Requests;

use LogicException;

abstract class Request
{
    /**
     * Define the HTTP method.
     */
    protected string $method;

    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass;

    /**
     * Get the method of the request.
     */
    public function getMethod(): string
    {
        if (!isset($this->method)) {
            throw new LogicException('Your request is missing a HTTP method. You must add a method property like [protected Method $method = Method::GET]');
        }

        return $this->method;
    }

    public function getQuery(): array
    {
        return [];
    }

    public static function getTargetResourceClass(): string
    {
        if (empty(static::$targetResourceClass)) {
            throw new \RuntimeException('Resource class is not set.');
        }

        return static::$targetResourceClass;
    }

    /**
     * Resolve the resource path.
     *
     * @return string
     */
    abstract public function resolveResourcePath(): string;
}
