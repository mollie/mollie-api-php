<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Utils\Utility;

class CreateCustomerPaymentRequestFactory extends RequestFactory
{
    private string $customerId;

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;
    }

    public function create(): CreateCustomerPaymentRequest
    {
        return new CreateCustomerPaymentRequest(
            $this->customerId,
            $this->payload('description'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            $this->payload('webhookUrl'),
            $this
                ->transformFromPayload(
                    'lines',
                    fn ($items) => OrderLineCollectionFactory::new($items)->create()
                ),
            $this->transformFromPayload('billingAddress', fn ($item) => Address::fromArray($item)),
            $this->transformFromPayload('shippingAddress', fn ($item) => Address::fromArray($item)),
            $this->payload('locale'),
            $this->payload('method'),
            $this->payload('issuer'),
            $this->payload('restrictPaymentMethodsToCountry'),
            $this->payload('metadata'),
            $this->payload('captureMode'),
            $this->payload('captureDelay'),
            $this->transformFromPayload(
                'applicationFee',
                fn ($item) => ApplicationFeeFactory::new($item)->create()
            ),
            $this->transformFromPayload(
                'routing',
                fn ($items) => PaymentRouteCollectionFactory::new($items)->create()
            ),
            $this->payload('sequenceType'),
            $this->payload('mandateId'),
            $this->payload('profileId'),
            $this->payload('additional') ?: Utility::filterByProperties(CreateCustomerPaymentRequest::class, $this->payload()) ?: [],
            $this->query('includeQrCode', false)
        );
    }
}
