<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreatePaymentRequest;

/**
 * @property CreatePaymentRequestableFactory $factory
 */
class CreatePaymentRequestFactory extends ComposableRequestFactory
{
    public function __construct()
    {
        $this->factory = CreatePaymentRequestableFactory::new();
    }

    public function create(): CreatePaymentRequest
    {
        return $this
            ->factory
            ->create($this);
    }

    public function compose(...$data): CreatePaymentRequest
    {
        return new CreatePaymentRequest(...$data);
    }
}
