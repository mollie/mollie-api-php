<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedPaymentLinkPaymentsRequest extends SortablePaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $paymentLinkId;

    public function __construct(string $paymentLinkId, ?string $from = null, ?int $limit = null, ?string $sort = null)
    {
        $this->paymentLinkId = $paymentLinkId;

        parent::__construct($from, $limit, $sort);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->paymentLinkId}/payments";
    }
}
