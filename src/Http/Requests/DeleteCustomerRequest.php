<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class DeleteCustomerRequest extends SimpleRequest
{
    protected static string $method = Method::DELETE;

    public static string $targetResourceClass = Customer::class;

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix(Customer::$resourceIdPrefix),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }
}
