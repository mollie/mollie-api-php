<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Requests\CreateDelayedPaymentRouteRequest;

class CreateDelayedPaymentRouteRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): CreateDelayedPaymentRouteRequest
    {
        if (! $this->payloadHas('amount')) {
            throw new LogicException('Amount is required');
        }

        if (! $destination = $this->payload('destination')) {
            throw new LogicException('Destination is required');
        }

        return new CreateDelayedPaymentRouteRequest(
            $this->paymentId,
            $this->transformFromPayload('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $destination,
        );
    }
}
