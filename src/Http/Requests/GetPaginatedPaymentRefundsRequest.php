<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentRefundsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = RefundCollection::class;

    private string $paymentId;

    public function __construct(
        string $paymentId,
        ?string $from = null,
        ?int $limit = null,
        bool $includePayment = false
    ) {
        $this->paymentId = $paymentId;

        parent::__construct($from, $limit);

        $this->query()
            ->add('include', $includePayment ? PaymentIncludesQuery::PAYMENT : null);
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds";
    }
}
