<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Types\Method;

class GetCustomerRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    protected $hydratableResource = Customer::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }
}
