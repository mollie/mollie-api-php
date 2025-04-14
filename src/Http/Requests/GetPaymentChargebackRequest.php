<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentChargebackRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Chargeback::class;

    private string $paymentId;

    private string $chargebackId;

    private bool $includePayment;

    public function __construct(string $paymentId, string $chargebackId, bool $includePayment = false)
    {
        $this->paymentId = $paymentId;
        $this->chargebackId = $chargebackId;
        $this->includePayment = $includePayment;
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/chargebacks/{$this->chargebackId}";
    }
}
