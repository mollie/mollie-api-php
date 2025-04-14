<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Resources\BalanceCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedBalanceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_balances()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceRequest::class => MockResponse::ok('balance-list'),
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
