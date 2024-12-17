<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Resources\BalanceCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetPaginatedBalanceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_balances()
    {
        $client = new MockClient([
            GetPaginatedBalanceRequest::class => new MockResponse(200, 'balance-list'),
        ]);

        $request = new GetPaginatedBalanceRequest;

        /** @var BalanceCollection */
        $balances = $client->send($request);

        $this->assertTrue($balances->getResponse()->successful());
        $this->assertInstanceOf(BalanceCollection::class, $balances);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedBalanceRequest;

        $this->assertEquals('balances', $request->resolveResourcePath());
    }
}
