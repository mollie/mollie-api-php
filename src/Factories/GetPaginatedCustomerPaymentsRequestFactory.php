<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;

class GetPaginatedCustomerPaymentsRequestFactory extends RequestFactory
{
    private string $customerId;

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;
    }

    public function create(): GetPaginatedCustomerPaymentsRequest
    {
        return new GetPaginatedCustomerPaymentsRequest(
            $this->customerId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('sort'),
            $this->query('profileId')
        );
    }
}
