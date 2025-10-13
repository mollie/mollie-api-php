<?php

namespace Mollie\Api\Resources;

use ArrayObject;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Traits\HasResponse;
use Mollie\Api\Utils\Arr;

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

    public function first()
    {
        return array_values($this->getArrayCopy())[0];
    }

    /**
     * @param  string|callable  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function firstWhere($key, $value = true)
    {
        if (! is_string($key) && is_callable($key)) {
            return $this->filter($key)->first();
        }

        return $this->filter(function ($item) use ($key, $value) {
            if (is_array($item)) {
                return Arr::get($item, $key) === $value;
            }

            return $item->{$key} === $value;
        })->first();
    }

    public static function getCollectionResourceName(): string
    {
        if (empty(static::$collectionName)) {
            throw new \RuntimeException('Collection name not set');
        }

        return static::$collectionName;
    }
}
