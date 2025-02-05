<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedSettlementRefundsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = RefundCollection::class;

    private string $settlementId;

    public function __construct(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        bool $includePayment = false
    ) {
        $this->settlementId = $settlementId;

        parent::__construct($from, $limit);

        $this->query()
            ->add('include', $includePayment ? PaymentIncludesQuery::PAYMENT : null);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "settlements/{$this->settlementId}/refunds";
    }
}
