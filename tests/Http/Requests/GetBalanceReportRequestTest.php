<?php

namespace Tests\Http\Requests;

use DateTime;
use Mollie\Api\Http\Data\GetBalanceReportQuery;
use Mollie\Api\Http\Requests\GetBalanceReportRequest;
use Mollie\Api\Resources\BalanceReport;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetBalanceReportRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_balance_report()
    {
        $client = new MockClient([
            GetBalanceReportRequest::class => new MockResponse(200, 'balance-report'),
        ]);

        $request = new GetBalanceReportRequest(
            'bal_12345',
            new GetBalanceReportQuery(
                new DateTime('2024-01-01'),
                new DateTime('2024-01-31'),
            )
        );

        /** @var BalanceReport */
        $balanceReport = $client->send($request);

        $this->assertTrue($balanceReport->getResponse()->successful());
        $this->assertInstanceOf(BalanceReport::class, $balanceReport);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetBalanceReportRequest('bal_12345', new GetBalanceReportQuery(
            new DateTime('2024-01-01'),
            new DateTime('2024-01-31'),
        ));

        $this->assertEquals('balances/bal_12345/report', $request->resolveResourcePath());
    }
}
