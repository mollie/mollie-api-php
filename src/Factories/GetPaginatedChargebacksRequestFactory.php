<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedChargebacksRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedChargebacksRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; Mollie uses `include=payment`.
        $includePayment = $this->queryHas('includePayment')
            ? (bool) $this->query('includePayment')
            : $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedChargebacksRequest(
            $this->query('from'),
            $this->query('limit'),
            $includePayment,
            $this->query('profileId')
        );
    }
}
