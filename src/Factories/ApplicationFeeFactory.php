<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\ApplicationFee;

class ApplicationFeeFactory extends Factory
{
    public function create(): ApplicationFee
    {
        return new ApplicationFee(
            MoneyFactory::new($this->data['amount'])->create(),
            $this->data['description'],
        );
    }
}
