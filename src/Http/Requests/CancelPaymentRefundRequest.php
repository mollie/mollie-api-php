<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CancelPaymentRefundRequest extends Request implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::DELETE;

    protected string $paymentId;

    protected string $id;

    public function __construct(string $paymentId, string $id)
    {
        $this->paymentId = $paymentId;
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds/{$this->id}";
    }
}
