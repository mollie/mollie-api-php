<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class RevokeMandateRequest extends Request implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private string $customerId;

    private string $mandateId;

    public function __construct(string $customerId, string $mandateId)
    {
        $this->customerId = $customerId;
        $this->mandateId = $mandateId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates/{$this->mandateId}";
    }
}
