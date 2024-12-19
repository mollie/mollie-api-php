<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Resources\Balance;
use PHPUnit\Framework\TestCase;

class GetBalanceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_balance()
    {
        $client = new MockMollieClient([
            GetBalanceRequest::class => MockResponse::ok('balance'),
        ]);

        $balanceId = 'bal_12345678';
        $request = new GetBalanceRequest($balanceId);

        /** @var Balance */
        $balance = $client->send($request);

        $this->assertTrue($balance->getResponse()->successful());
        $this->assertInstanceOf(Balance::class, $balance);
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
