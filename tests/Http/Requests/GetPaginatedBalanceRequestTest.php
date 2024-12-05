<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BalanceCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedBalanceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_balances()
    {
        $client = new MockClient([
            GetPaginatedBalanceRequest::class => new MockResponse(200, 'balance-list'),
        ]);

        $request = new GetPaginatedBalanceRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(BalanceCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedBalanceRequest;

        $this->assertEquals('balances', $request->resolveResourcePath());
    }
}
