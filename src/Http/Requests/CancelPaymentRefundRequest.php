<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

class CancelPaymentRefundRequest extends Request implements SupportsTestmodeInQuery
{
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
