<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/refunds-api/cancel-refund
 */
class CancelPaymentRefundRequest extends Request implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::DELETE;

    public function __construct(
        private string $paymentId,
        private string $id,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds/{$this->id}";
    }
}
