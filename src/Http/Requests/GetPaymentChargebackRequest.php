<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaymentChargebackQuery;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Types\Method;

class GetPaymentChargebackRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Chargeback::class;

    private string $paymentId;

    private string $chargebackId;

    private ?GetPaymentChargebackQuery $query;

    public function __construct(string $paymentId, string $chargebackId, ?GetPaymentChargebackQuery $query = null)
    {
        $this->paymentId = $paymentId;
        $this->chargebackId = $chargebackId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/chargebacks/{$this->chargebackId}";
    }
}
