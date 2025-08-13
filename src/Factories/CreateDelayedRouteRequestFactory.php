<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Requests\CreateDelayedRouteRequest;

class CreateDelayedRouteRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): CreateDelayedRouteRequest
    {
        if (! $amount = $this->payload('amount')) {
            throw new LogicException('Amount is required');
        }

        if (! $destination = $this->payload('destination')) {
            throw new LogicException('Destination is required');
        }

        return new CreateDelayedRouteRequest(
            $this->paymentId,
            MoneyFactory::new($amount)->create(),
            $destination,
        );
    }
}
