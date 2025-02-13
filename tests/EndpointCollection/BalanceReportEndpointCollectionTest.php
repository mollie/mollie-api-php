<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetBalanceReportRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceReport;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Traits\AmountObjectTestHelpers;
use Tests\Fixtures\Traits\LinkObjectTestHelpers;

class BalanceReportEndpointCollectionTest extends TestCase
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;

    /** @test */
    public function get_for_id()
    {
        $client = new MockMollieClient([
            GetBalanceReportRequest::class => MockResponse::ok('balance-report', 'bal_gVMhHKqSSRYJyPsuoPNFH'),
        ]);

        /** @var BalanceReport $report */
        $report = $client->balanceReports->getForId('bal_gVMhHKqSSRYJyPsuoPNFH', [
            'from' => '2021-01-01',
            'until' => '2021-02-01',
            'grouping' => 'transaction-categories',
        ]);

        $this->assertBalanceReport($report, 'bal_gVMhHKqSSRYJyPsuoPNFH');
    }

    /** @test */
    public function get_for_balance()
    {
        $client = new MockMollieClient([
            GetBalanceReportRequest::class => MockResponse::ok('balance-report', 'bal_gVMhHKqSSRYJyPsuoPNFH'),
        ]);

        $balance = new Balance($client);
        $balance->id = 'bal_gVMhHKqSSRYJyPsuoPNFH';

        /** @var BalanceReport $report */
        $report = $client->balanceReports->getFor($balance, [
            'from' => '2021-01-01',
            'until' => '2021-02-01',
            'grouping' => 'transaction-categories',
        ]);

        $this->assertBalanceReport($report, 'bal_gVMhHKqSSRYJyPsuoPNFH');
    }

    /** @test */
    public function get_for_primary()
    {
        $client = new MockMollieClient([
            GetBalanceReportRequest::class => MockResponse::ok('balance-report', 'bal_primary'),
        ]);

        /** @var BalanceReport $report */
        $report = $client->balanceReports->getForPrimary([
            'from' => '2024-01-01',
            'until' => '2024-01-31',
            'grouping' => 'transaction-categories',
        ]);

        $this->assertBalanceReport($report, 'bal_primary');
    }

    protected function assertBalanceReport(BalanceReport $report, string $balanceId)
    {
        $this->assertInstanceOf(BalanceReport::class, $report);
        $this->assertEquals('balance-report', $report->resource);
        $this->assertEquals($balanceId, $report->balanceId);
        $this->assertNotEmpty($report->timeZone);
        $this->assertNotEmpty($report->from);
        $this->assertNotEmpty($report->until);
        $this->assertNotEmpty($report->grouping);

        $this->assertNotNull($report->totals->open->available->amount);
        $this->assertNotNull($report->totals->open->pending->amount);
        $this->assertNotNull($report->totals->payments->immediatelyAvailable->amount);
        $this->assertNotNull($report->totals->payments->pending->amount);
    }
}
