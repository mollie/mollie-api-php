<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

class DeletePaymentLinkRequest extends Request implements SupportsTestmodeInQuery
{
    protected static string $method = Method::DELETE;

    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
