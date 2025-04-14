<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;

class GetSettlementRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_settlement()
    {
        $client = new MockMollieClient([
            GetSettlementRequest::class => MockResponse::ok('settlement'),
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
