<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\Discount;
use Mollie\Api\Http\Payload\InvoiceLine;

class InvoiceLineFactory extends Factory
{
    public function create(): InvoiceLine
    {
        return new InvoiceLine(
            $this->get('description'),
            $this->get('quantity'),
            $this->get('vatRate'),
            MoneyFactory::new($this->get('unitPrice'))->create(),
            $this->mapIfNotNull('discount', fn (array $data) => Discount::fromArray($data))
        );
    }
}