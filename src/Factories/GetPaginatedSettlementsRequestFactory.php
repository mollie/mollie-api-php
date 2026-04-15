<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;

class GetPaginatedSettlementsRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedSettlementsRequest
    {
        return new GetPaginatedSettlementsRequest(
            $this->query('from'),
            $this->query('limit'),
            $this->query('balanceId'),
        );
    }
}
