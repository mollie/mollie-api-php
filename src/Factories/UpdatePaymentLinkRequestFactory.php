<?php

namespace Mollie\Api\Factories;

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
        );
    }
}
