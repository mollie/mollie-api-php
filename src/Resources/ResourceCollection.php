<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Http\Response;

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

    /**
     * @return $this
     */
    public static function withResponse(Response $response, Connector $connector, $items = [], ?\stdClass $_links = null): self
    {
        $collection = new static($connector, $items, $_links);

        return $collection->setResponse($response);
    }
}
