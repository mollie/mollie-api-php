<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreatePayoutRequest;

class CreatePayoutRequestFactory extends RequestFactory
{
    public function create(): CreatePayoutRequest
    {
        return new CreatePayoutRequest(
            $this->payload('balanceId'),
            $this->transformFromPayload('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->payload('description')
        );
    }
}
