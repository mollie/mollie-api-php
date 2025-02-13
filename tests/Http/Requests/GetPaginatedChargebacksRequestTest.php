<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Resources\ChargebackCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedChargebacksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_chargebacks()
    {
        $client = new MockMollieClient([
            GetPaginatedChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ]);

        $request = new GetPaginatedChargebacksRequest;

        /** @var ChargebackCollection */
        $chargebacks = $client->send($request);

        $this->assertTrue($chargebacks->getResponse()->successful());
        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedChargebacksRequest;

        $this->assertEquals('chargebacks', $request->resolveResourcePath());
    }
}
