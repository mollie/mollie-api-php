<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\DataCollection;

class OrderLineCollectionFactory extends OldFactory
{
    public function create(): DataCollection
    {
        return new DataCollection(array_map(
            fn(array $item) => OrderLineFactory::new($item)->create(),
            $this->data
        ));
    }
}
