<?php

namespace Mollie\Api\Resources;

use ArrayObject;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Traits\HasResponse;

abstract class BaseCollection extends ArrayObject implements IsResponseAware
{
    use HasResponse;

    protected Connector $connector;

    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = '';

    public ?\stdClass $_links = null;

    /**
     * @param  array|object  $items
     */
    public function __construct(Connector $connector, $items = [], ?\stdClass $_links = null)
    {
        parent::__construct($items);

        $this->_links = $_links;
        $this->connector = $connector;
    }

    public function contains(callable $callback): bool
    {
        foreach ($this as $item) {
            if ($callback($item)) {
                return true;
            }
        }

        return false;
    }

    public function filter(callable $callback)
    {
        $filteredItems = array_filter($this->getArrayCopy(), $callback);

        /** @phpstan-ignore-next-line */
        return (new static($this->connector, $filteredItems,  $this->_links))->setResponse($this->response);
    }

    public static function getCollectionResourceName(): string
    {
        if (empty(static::$collectionName)) {
            throw new \RuntimeException('Collection name not set');
        }

        return static::$collectionName;
    }
}
