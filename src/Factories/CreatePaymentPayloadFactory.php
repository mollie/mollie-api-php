<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Utils\Utility;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\Metadata;

class CreatePaymentPayloadFactory extends Factory
{
    public function create(): CreatePaymentPayload
    {
        return new CreatePaymentPayload(
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
            $this->get('additional') ?? Utility::filterByProperties(CreatePaymentPayload::class, $this->data),
        );
    }
}
