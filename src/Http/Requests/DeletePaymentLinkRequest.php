<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class DeletePaymentLinkRequest extends SimpleRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::DELETE;

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
