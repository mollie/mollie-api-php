<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Types\Method;

class DeleteCustomerRequest extends SimpleRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::DELETE;

    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }
}
