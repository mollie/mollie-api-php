<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Utils\Arr;

/**
 * @property CreatePaymentRequestableFactory $factory
 */
class CreateCustomerPaymentRequestFactory extends ComposableRequestFactory
{
    private string $customerId;

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;

        $this->factory = CreatePaymentRequestableFactory::new();
    }

    public function create(): CreateCustomerPaymentRequest
    {
        return $this
            ->factory
            ->create($this);
    }

    public function compose(...$data): CreateCustomerPaymentRequest
    {
        return new CreateCustomerPaymentRequest($this->customerId, ...Arr::except($data, ['customerId']));
    }
}
