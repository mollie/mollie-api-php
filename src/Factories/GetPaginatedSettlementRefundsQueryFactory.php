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
        $includePayment = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementRefundsRequest(
            $this->settlementId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayment', $includePayment)
        );
    }
}
