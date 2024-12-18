<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetSettlementRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_settlement()
    {
        $client = new MockClient([
            GetSettlementRequest::class => new MockResponse(200, 'settlement'),
        ]);

        $request = new GetSettlementRequest('stl_jDk30akdN');

        /** @var Settlement */
        $settlement = $client->send($request);

        $this->assertTrue($settlement->getResponse()->successful());
        $this->assertInstanceOf(Settlement::class, $settlement);
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
