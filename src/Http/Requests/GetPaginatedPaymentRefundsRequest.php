<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\GetPaginatedPaymentRefundQuery;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedPaymentRefundsRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\RefundCollection::class;

    private string $paymentId;

    public function __construct(
        string $paymentId,
        ?GetPaginatedPaymentRefundQuery $query = null
    ) {
        parent::__construct($query);

        $this->paymentId = $paymentId;
    }

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix(Payment::$resourceIdPrefix),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds";
    }
}
