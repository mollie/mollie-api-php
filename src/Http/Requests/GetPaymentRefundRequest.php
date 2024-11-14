<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\GetPaymentRefundQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class GetPaymentRefundRequest extends Request
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

    private GetPaymentRefundQuery $query;

    public function __construct(string $paymentId, string $refundId, GetPaymentRefundQuery $query)
    {
        $this->paymentId = $paymentId;
        $this->refundId = $refundId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function rules(): array
    {
        return [
            'paymentId' => Id::startsWithPrefix(Payment::$resourceIdPrefix),
            'refundId' => Id::startsWithPrefix(Refund::$resourceIdPrefix),
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
