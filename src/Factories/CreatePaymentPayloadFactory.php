<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\Address;
use Mollie\Api\Http\Payload\CreatePayment;
use Mollie\Api\Http\Payload\Metadata;

class CreatePaymentPayloadFactory extends Factory
{
    public function create(): CreatePayment
    {
        return new CreatePayment(
            $this->get('description'),
            MoneyFactory::new($this->get('amount'))->create(),
            $this->get('redirectUrl'),
            $this->get('cancelUrl'),
            $this->get('webhookUrl'),
            $this
                ->mapIfNotNull(
                    'lines',
                    fn (array $items) => OrderLineCollectionFactory::new($items)->create()
                ),
            $this->mapIfNotNull('billingAddress', fn (array $item) => Address::fromArray($item)),
            $this->mapIfNotNull('shippingAddress', fn (array $item) => Address::fromArray($item)),
            $this->get('locale'),
            $this->get('method'),
            $this->get('issuer'),
            $this->get('restrictPaymentMethodsToCountry'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('captureMode'),
            $this->get('captureDelay'),
            $this->mapIfNotNull(
                'applicationFee',
                fn (array $item) => ApplicationFeeFactory::new($item)->create()
            ),
            $this->mapIfNotNull(
                'routing',
                fn (array $items) => PaymentRouteCollectionFactory::new($items)->create()
            ),
            $this->get('sequenceType'),
            $this->get('mandateId'),
            $this->get('customerId'),
            $this->get('profileId'),
            $this->get('additional') ?? Helpers::filterByProperties(CreatePayment::class, $this->data),
            $this->get('testmode')
        );
    }
}
