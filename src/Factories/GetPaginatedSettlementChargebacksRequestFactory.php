<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedSettlementChargebacksRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedSettlementChargebacksRequestFactory extends RequestFactory
{
    private string $settlementId;

    public function __construct(string $settlementId)
    {
        $this->settlementId = $settlementId;
    }

    public function create(): GetPaginatedSettlementChargebacksRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; Mollie uses `include=payment`.
        $includePayment = $this->queryHas('includePayment')
            ? (bool) $this->query('includePayment')
            : $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementChargebacksRequest(
            $this->settlementId,
            $this->query('from'),
            $this->query('limit'),
            $includePayment,
            $this->query('profileId'),
        );
    }
}
