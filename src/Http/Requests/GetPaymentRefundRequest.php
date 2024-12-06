<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaymentRefundQuery;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\Method;

class GetPaymentRefundRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Refund::class;

    private string $paymentId;

    private string $refundId;

    private ?GetPaymentRefundQuery $query = null;

    public function __construct(string $paymentId, string $refundId, ?GetPaymentRefundQuery $query = null)
    {
        $this->paymentId = $paymentId;
        $this->refundId = $refundId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds/{$this->refundId}";
    }
}
