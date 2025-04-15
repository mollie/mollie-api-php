<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Utils\Utility;

class UpdatePaymentRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): UpdatePaymentRequest
    {
        return new UpdatePaymentRequest(
            $this->id,
            $this->payload('description'),
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            $this->payload('webhookUrl'),
            $this->payload('metadata'),
            $this->payload('method'),
            $this->payload('locale'),
            $this->payload('restrictPaymentMethodsToCountry'),
            $this->payload('additional') ?? Utility::filterByProperties(UpdatePaymentRequest::class, $this->payload()),
        );
    }
}
