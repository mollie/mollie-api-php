<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\ApplicationFee;

class ApplicationFeeFactory extends Factory
{
    public function create(): ApplicationFee
    {
        return new ApplicationFee(
            MoneyFactory::new($this->get('amount'))->create(),
            $this->get('description'),
        );
    }
}
