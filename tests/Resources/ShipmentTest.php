<?php

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Shipment;
use PHPUnit\Framework\TestCase;

class ShipmentTest extends TestCase
{
    public function test_has_tracking_returns_true_if_object_not_null()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy();
        $this->assertTrue($shipment->hasTracking());
    }

    public function test_has_tracking_returns_false_if_object_is_null()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = null;
        $this->assertFalse($shipment->hasTracking());
    }

    public function test_has_tracking_url_returns_false_if_tracking_is_not_set()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = null;
        $this->assertFalse($shipment->hasTrackingUrl());
    }

    public function test_has_tracking_url_returns_true_if_url_is_set()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy([
            'url' => 'https://www.some-tracking-url.com/123',
        ]);
        $this->assertTrue($shipment->hasTrackingUrl());
    }

    public function test_has_tracking_url_returns_false_if_url_is_not_set()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));
        $shipment->tracking = $this->getTrackingDummy([
            'url' => null,
        ]);
        $this->assertFalse($shipment->hasTrackingUrl());
    }

    public function test_get_tracking_url_returns_null_if_not_available()
    {
        $shipment = new Shipment($this->createMock(MollieApiClient::class));

        $shipment->tracking = null;
        $this->assertNull($shipment->getTrackingUrl());

        $shipment->tracking = $this->getTrackingDummy([
            'url' => null,
        ]);
        $this->assertNull($shipment->getTrackingUrl());
    }

    public function test_get_tracking_url_returns_url_if_available()
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
