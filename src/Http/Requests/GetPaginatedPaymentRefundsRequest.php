<?php

namespace Mollie\Api\Http\Requests;

class GetPaginatedPaymentRefundsRequest extends PaginatedRequest
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\RefundCollection::class;

    protected string $paymentId;

    public function __construct(
        string $paymentId,
        array $filters = []
    ) {
        parent::__construct(filters: $filters);

        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}/refunds";
    }
}
