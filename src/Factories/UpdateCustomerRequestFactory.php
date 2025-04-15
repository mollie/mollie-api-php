<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\UpdateCustomerRequest;

class UpdateCustomerRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): UpdateCustomerRequest
    {
        return new UpdateCustomerRequest(
            $this->id,
            $this->payload('name'),
            $this->payload('email'),
            $this->payload('locale'),
            $this->payload('metadata'),
        );
    }
}
