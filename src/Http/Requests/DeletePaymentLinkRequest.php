<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DeletePaymentLinkRequest extends Request implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

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
