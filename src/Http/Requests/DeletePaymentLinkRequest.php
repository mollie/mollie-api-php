<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Types\Method;

class DeletePaymentLinkRequest extends SimpleRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::DELETE;

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
