<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedSettlementRefundsQueryFactory extends RequestFactory
{
    private string $settlementId;

    public function __construct(string $settlementId)
    {
        $this->settlementId = $settlementId;
    }

    public function create(): GetPaginatedSettlementRefundsRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; this endpoint uses `embed=payment`.
        $includePayment = $this->queryIncludes('embed', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementRefundsRequest(
            $this->settlementId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayment', $includePayment),
        );
    }
}
