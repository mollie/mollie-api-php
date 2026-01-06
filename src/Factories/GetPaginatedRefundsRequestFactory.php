<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedRefundsRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedRefundsRequest
    {
        // Legacy: historically this factory accepted `embedPayment` directly; Mollie uses `embed=payment`.
        $embedPayment = $this->queryHas('embedPayment')
            ? (bool) $this->query('embedPayment')
            : $this->queryIncludes('embed', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedRefundsRequest(
            $this->query('from'),
            $this->query('limit'),
            $embedPayment,
            $this->query('profileId')
        );
    }
}
