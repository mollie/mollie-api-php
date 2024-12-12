<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaginatedPaymentChargebacksQuery;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedPaymentChargebacksRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = ChargebackCollection::class;

    private string $paymentId;

    public function __construct(string $paymentId, ?GetPaginatedPaymentChargebacksQuery $query = null)
    {
        parent::__construct($query);

        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/chargebacks";
    }
}
