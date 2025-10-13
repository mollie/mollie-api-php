<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Resources\ResourceCollection;

class ListResponseBuilder
{
    protected string $collectionClass;

    protected array $items = [];

    public function __construct(
        string $collectionClass
    ) {
        if (! is_subclass_of($collectionClass, ResourceCollection::class)) {
            throw new LogicException('Collection class must be a subclass of '.ResourceCollection::class);
        }

        $this->collectionClass = $collectionClass;
    }

    public function add(array $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function addMany(array $items): self
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    public function create(): MockResponse
    {
        $contents = FakeResponseLoader::load('empty-list');

        $collectionKey = $this->collectionClass::$collectionName;
        $contents = str_replace('{{ RESOURCE_ID }}', $collectionKey, $contents);

        $data = json_decode($contents, true);

        $data['count'] = count($this->items);
        $data['_embedded'][$collectionKey] = $this->items;

        return new MockResponse($data);
    }
}
