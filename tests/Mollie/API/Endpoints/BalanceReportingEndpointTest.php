<?php

declare(strict_types=1);

namespace Tests\Mollie\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class BalanceReportingEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testGetBalanceReport()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances/some_id/reporting"
            ),
            new Response(
                200,
                [],
                ''
            )
        );
        // $api->balanceReporting();
        // $balance->reporting();
        $this->markTestIncomplete("TBI BalanceReportingEndpointTest");
    }
}
