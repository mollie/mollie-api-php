<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\DataCollection;

class OrderLineCollectionFactory extends Factory
{
    public function create(): DataCollection
    {
        return new DataCollection(array_map(
            fn ($item) => OrderLineFactory::new($item)->create(),
            $this->get()
        ));
    }
}
