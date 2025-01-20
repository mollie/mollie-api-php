<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Utils\Utility;

class CreatePaymentRequestFactory extends Factory
{
    public static function new(): self
    {
        return new self();
    }

    public function create(): CreatePaymentRequest
    {
        return new CreatePaymentRequest(
            $this->payload('description'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            $this->payload('webhookUrl'),
            $this
                ->mapIfNotNull(
                    'lines',
                    fn (array $items) => OrderLineCollectionFactory::new($items)->create()
                ),
            $this->mapIfNotNull('billingAddress', fn (array $item) => Address::fromArray($item)),
            $this->mapIfNotNull('shippingAddress', fn (array $item) => Address::fromArray($item)),
            $this->payload('locale'),
            $this->payload('method'),
            $this->payload('issuer'),
            $this->payload('restrictPaymentMethodsToCountry'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->payload('captureMode'),
            $this->payload('captureDelay'),
            $this->mapIfNotNull(
                'applicationFee',
                fn (array $item) => ApplicationFeeFactory::new($item)->create()
            ),
            $this->mapIfNotNull(
                'routing',
                fn (array $items) => PaymentRouteCollectionFactory::new($items)->create()
            ),
            $this->payload('sequenceType'),
            $this->payload('mandateId'),
            $this->payload('customerId'),
            $this->payload('profileId'),
            $this->payload('additional') ?? Utility::filterByProperties(CreatePaymentPayload::class, $this->payload),
            $this->query('includeQrCode', false)
        );
    }
}
