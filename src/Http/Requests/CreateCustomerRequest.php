<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\CreateCustomerPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateCustomerRequest extends Request implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected static string $targetResourceClass = Customer::class;

    private CreateCustomerPayload $payload;

    public function __construct(CreateCustomerPayload $payload)
    {
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return 'customers';
    }
}
