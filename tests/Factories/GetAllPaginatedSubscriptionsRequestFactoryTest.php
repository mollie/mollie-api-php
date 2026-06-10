<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Factories\GetAllPaginatedSubscriptionsRequestFactory;
use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllPaginatedSubscriptionsRequestFactoryTest extends TestCase
{
    #[Test]
    public function create_returns_all_paginated_subscriptions_request_object_with_full_data()
    {
        $request = GetAllPaginatedSubscriptionsRequestFactory::new()
            ->withQuery([
                'limit' => 50,
                'from' => 'sub_12345',
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetAllPaginatedSubscriptionsRequest::class, $request);
    }

    #[Test]
    public function create_returns_all_paginated_subscriptions_request_object_with_minimal_data()
    {
        $request = GetAllPaginatedSubscriptionsRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetAllPaginatedSubscriptionsRequest::class, $request);
    }

    #[Test]
    public function create_returns_all_paginated_subscriptions_request_object_with_partial_data()
    {
        $request = GetAllPaginatedSubscriptionsRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'from' => 'sub_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetAllPaginatedSubscriptionsRequest::class, $request);
    }
}
