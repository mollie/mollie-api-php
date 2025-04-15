<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;

class CreatePaymentCaptureRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): CreatePaymentCaptureRequest
    {
        return new CreatePaymentCaptureRequest(
            $this->paymentId,
            $this->payload('description'),
            $this->transformFromPayload('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->payload('metadata')
        );
    }
}
