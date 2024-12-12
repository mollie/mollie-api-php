<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Settlement;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetSettlementRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_settlement()
    {
        $client = new MockClient([
            GetSettlementRequest::class => new MockResponse(200, 'settlement'),
        ]);

        $request = new GetSettlementRequest('stl_jDk30akdN');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Settlement */
        $settlement = $response->toResource();

        $this->assertInstanceOf(Settlement::class, $settlement);
        $this->assertEquals('settlement', $settlement->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetSettlementRequest('stl_jDk30akdN');

        $this->assertEquals(
            'settlements/stl_jDk30akdN',
            $request->resolveResourcePath()
        );
    }
}
