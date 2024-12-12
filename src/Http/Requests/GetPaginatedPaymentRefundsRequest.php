<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaginatedPaymentRefundQuery;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedPaymentRefundsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = RefundCollection::class;

    private string $paymentId;

    public function __construct(
        string $paymentId,
        ?GetPaginatedPaymentRefundQuery $query = null
    ) {
        parent::__construct($query);

        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds";
    }
}
