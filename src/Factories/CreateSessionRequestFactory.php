<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreateSessionRequest;

class CreateSessionRequestFactory extends RequestFactory
{
    public function create(): CreateSessionRequest
    {
        return new CreateSessionRequest(
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->payload('description'),
            $this->payload('method'),
            $this->payload('checkoutFlow')
        );
    }
}
