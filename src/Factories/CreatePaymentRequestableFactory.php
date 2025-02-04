<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Utils\Utility;

class CreatePaymentRequestableFactory extends RequestFactory
{
    /**
     * @param  CreatePaymentRequestFactory|CreateCustomerPaymentRequestFactory  $composableFactory
     * @return CreatePaymentRequest|CreateCustomerPaymentRequest
     */
    public function create($composableFactory): Request
    {
        if (! $composableFactory instanceof CreatePaymentRequestFactory && ! $composableFactory instanceof CreateCustomerPaymentRequestFactory) {
            throw new LogicException('Invalid request class');
        }

        return $composableFactory->compose(
            $this->payload('description'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            $this->payload('webhookUrl'),
            $this
                ->transformFromPayload(
                    'lines',
                    fn (array $items) => OrderLineCollectionFactory::new($items)->create()
                ),
            $this->transformFromPayload('billingAddress', fn (array $item) => Address::fromArray($item)),
            $this->transformFromPayload('shippingAddress', fn (array $item) => Address::fromArray($item)),
            $this->payload('locale'),
            $this->payload('method'),
            $this->payload('issuer'),
            $this->payload('restrictPaymentMethodsToCountry'),
            $this->transformFromPayload('metadata', Metadata::class),
            $this->payload('captureMode'),
            $this->payload('captureDelay'),
            $this->transformFromPayload(
                'applicationFee',
                fn (array $item) => ApplicationFeeFactory::new($item)->create()
            ),
            $this->transformFromPayload(
                'routing',
                fn (array $items) => PaymentRouteCollectionFactory::new($items)->create()
            ),
            $this->payload('sequenceType'),
            $this->payload('mandateId'),
            $this->payload('customerId'),
            $this->payload('profileId'),
            $this->payload('additional') ?? Utility::filterByProperties(CreatePaymentRequest::class, $this->payload),
            $this->query('includeQrCode', false)
        );
    }
}
