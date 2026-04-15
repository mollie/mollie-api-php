<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;

class GetAllPaginatedSubscriptionsRequestFactory extends RequestFactory
{
    public function create(): GetAllPaginatedSubscriptionsRequest
    {
        return new GetAllPaginatedSubscriptionsRequest(
            from: $this->query('from'),
            limit: $this->query('limit'),
            profileId: $this->query('profileId'),
        );
    }
}
