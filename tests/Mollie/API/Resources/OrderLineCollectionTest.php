<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\OrderLineCollection;

class OrderLineCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testCanGetOrderLine()
    {
        $mockApi = $this->createMock(MollieApiClient::class);

        $line1 = new OrderLine($mockApi);
        $line1->id = 'odl_aaaaaaaaaaa1';

        $line2 = new OrderLine($mockApi);
        $line2->id = 'odl_aaaaaaaaaaa2';

        $line3 = new OrderLine($mockApi);
        $line3->id = 'odl_aaaaaaaaaaa3';

        $lines = new OrderLineCollection($mockApi, [
            $line1,
            $line2,
            $line3,
        ], null);

        $this->assertNull($lines->get('odl_not_existent'));

        $line = $lines->get('odl_aaaaaaaaaaa2');

        $this->assertInstanceOf(OrderLine::class, $line);
        $this->assertEquals($line2, $line);
    }
}
