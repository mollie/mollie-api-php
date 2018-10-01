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

    public function testHasTrackingUrlReturnsFalseIfTrackingIsNotSet()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = null;
        $this->assertFalse($shipment->hasTrackingUrl());
    }

    public function testHasTrackingUrlReturnsTrueIfUrlIsSet()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy([
            'url' => 'https://www.some-tracking-url.com/123',
        ]);
        $this->assertTrue($shipment->hasTrackingUrl());
    }

    public function testHasTrackingUrlReturnsFalseIfUrlIsNotSet()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy([
            'url' => null,
        ]);
        $this->assertFalse($shipment->hasTrackingUrl());
    }

    public function testGetTrackingUrlReturnsNullIfNotAvailable()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));

        $shipment->tracking = null;
        $this->assertNull($shipment->getTrackingUrl());

        $shipment->tracking = $this->getTrackingDummy([
            'url' => null,
        ]);
        $this->assertNull($shipment->getTrackingUrl());
    }

    public function testGetTrackingUrlReturnsUrlIfAvailable()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy([
            'url' => 'https://www.some-tracking-url.com/123',
        ]);

        $this->assertEquals(
            'https://www.some-tracking-url.com/123',
            $shipment->getTrackingUrl()
        );
    }

    protected function getTrackingDummy($overrides = [])
    {
        return (object) array_merge([
            'carrier' => 'DummyCarrier',
            'code' => '123456ABCD',
            'url' => 'https://www.example.org/tracktrace/1234',
        ], $overrides);
    }
}
