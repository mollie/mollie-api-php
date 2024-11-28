<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\SortablePaginatedQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedPaymentLinkPaymentsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PaymentCollection::class;

    private string $paymentLinkId;

    public function __construct(string $paymentLinkId, ?SortablePaginatedQuery $query = null)
    {
        parent::__construct($query);
        $this->paymentLinkId = $paymentLinkId;
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->paymentLinkId}/payments";
    }
}