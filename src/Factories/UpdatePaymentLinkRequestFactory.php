<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;

class UpdatePaymentLinkRequestFactory extends RequestFactory
{
    private string $paymentLinkId;

    public function __construct(string $paymentLinkId)
    {
        $this->paymentLinkId = $paymentLinkId;
    }

    public function create(): UpdatePaymentLinkRequest
    {
        return new UpdatePaymentLinkRequest(
            $this->paymentLinkId,
            $this->payload('description'),
            $this->payload('archived', false),
            $this->payload('allowedMethods'),
            $this->transformFromPayload('lines', fn ($items) => ! empty($items) ? OrderLineCollectionFactory::new($items)->create() : null),
            $this->transformFromPayload('billingAddress', fn ($item) => Address::fromArray($item)),
            $this->transformFromPayload('shippingAddress', fn ($item) => Address::fromArray($item)),
            $this->transformFromPayload('minimumAmount', fn ($amount) => MoneyFactory::new($amount)->create()),
        );
    }
}
