<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Balance;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetBalanceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_balance()
    {
        $client = new MockClient([
            GetBalanceRequest::class => new MockResponse(200, 'balance'),
        ]);

        $balanceId = 'bal_12345678';
        $request = new GetBalanceRequest($balanceId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Balance::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $balanceId = 'bal_12345678';
        $request = new GetBalanceRequest($balanceId);

        $this->assertEquals(
            "balances/{$balanceId}",
            $request->resolveResourcePath()
        );
    }
}
