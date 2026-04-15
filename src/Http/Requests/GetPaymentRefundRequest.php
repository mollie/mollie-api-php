<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;

/**
 * @see https://docs.mollie.com/reference/v2/refunds-api/get-refund
 */
class GetPaymentRefundRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Refund::class;

    public function __construct(
        private string $paymentId,
        private string $refundId,
        private bool $includePayment = false,
    )
    {
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds/{$this->refundId}";
    }
}
