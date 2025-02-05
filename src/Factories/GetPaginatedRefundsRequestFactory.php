<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedRefundsRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedRefundsRequest
    {
        $embedPayment = $this->queryIncludes('embed', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedRefundsRequest(
            $this->query('from'),
            $this->query('limit'),
            $this->query('embedPayment', $embedPayment),
            $this->query('profileId')
        );
    }
}
