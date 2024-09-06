<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class GetCustomerRequest extends SimpleRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Customer::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix(Customer::$resourceIdPrefix),
        ];
    }
}
