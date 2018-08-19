<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Shipment;
use PHPUnit\Framework\TestCase;

class ShipmentTest extends TestCase
{
    public function testHasTrackingReturnsTrueIfObjectNotNull()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy();
        $this->assertTrue($shipment->hasTracking());
    }

    public function testHasTrackingReturnsFalseIfObjectIsNull()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = null;
        $this->assertFalse($shipment->hasTracking());
    }

    protected function getTrackingDummy()
    {
        return (object) [];
    }
}
