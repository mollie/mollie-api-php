<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedSettlementCapturesRequestFactory extends RequestFactory
{
    private string $settlementId;

    public function __construct(string $settlementId)
    {
        $this->settlementId = $settlementId;
    }

    public function create(): GetPaginatedSettlementCapturesRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; this endpoint uses `embed=payment`.
        $includePayment = $this->queryIncludes('embed', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementCapturesRequest(
            $this->settlementId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayment', $includePayment),
        );
    }
}
