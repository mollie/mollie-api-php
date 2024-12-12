<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\UpdateCustomerPayload;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateCustomerRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Customer::class;

    private string $id;

    private UpdateCustomerPayload $payload;

    public function __construct(string $id, UpdateCustomerPayload $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }
}
