<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\OrderLine;

class OrderLineFactory extends Factory
{
    public function create(): OrderLine
    {
        return new OrderLine(
            $this->get('description'),
            $this->get('quantity'),
            MoneyFactory::new($this->get('unitPrice'))->create(),
            MoneyFactory::new($this->get('totalAmount'))->create(),
            $this->get('type'),
            $this->get('quantityUnit'),
            $this->mapIfNotNull('discountAmount', fn(array $item) => MoneyFactory::new($item)->create()),
            $this->mapIfNotNull('recurring', fn(array $item) => RecurringBillingCycleFactory::new($item)->create()),
            $this->get('vatRate'),
            $this->mapIfNotNull('vatAmount', fn(array $item) => MoneyFactory::new($item)->create()),
            $this->get('sku'),
            $this->get('imageUrl'),
            $this->get('productUrl'),
        );
    }
}
