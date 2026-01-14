<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Requests\CreateSessionRequest;

class CreateSessionRequestFactory extends RequestFactory
{
    public function create(): CreateSessionRequest
    {
        return new CreateSessionRequest(
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->payload('description'),
            $this->payload('redirectUrl'),
            $this->transformFromPayload(
                'lines',
                fn ($items) => OrderLineCollectionFactory::new($items)->create()
            ),
            $this->payload('cancelUrl'),
            $this->transformFromPayload('billingAddress', fn ($item) => Address::fromArray($item)),
            $this->transformFromPayload('shippingAddress', fn ($item) => Address::fromArray($item)),
            $this->payload('customerId'),
            $this->payload('sequenceType'),
            $this->payload('metadata'),
            $this->payload('webhookUrl', null, 'payment.'),
            $this->payload('profileId')
        );
    }
}
