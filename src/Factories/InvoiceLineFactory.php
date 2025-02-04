<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\InvoiceLine;

class InvoiceLineFactory extends Factory
{
    public function create(): InvoiceLine
    {
        return new InvoiceLine(
            $this->get('description'),
            $this->get('quantity'),
            $this->get('vatRate'),
            MoneyFactory::new($this->get('unitPrice'))->create(),
            $this->transformIfNotNull('discount', fn (array $data) => Discount::fromArray($data))
        );
    }
}
