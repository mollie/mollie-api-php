<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetCapabilityRequest;
use Mollie\Api\Http\Requests\ListCapabilitiesRequest;
use Mollie\Api\Resources\Capability;
use Mollie\Api\Resources\CapabilityCollection;
use PHPUnit\Framework\TestCase;

class CapabilityEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetCapabilityRequest::class => MockResponse::ok('capability'),
        ]);

        /** @var Capability $capability */
        $capability = $client->capabilities->get('payments');

        $this->assertInstanceOf(Capability::class, $capability);
    }

    /** @test */
    public function list()
    {
        $client = new MockMollieClient([
            ListCapabilitiesRequest::class => MockResponse::ok('capability-list'),
        ]);

        /** @var CapabilityCollection $capabilities */
        $capabilities = $client->capabilities->list();

        $this->assertInstanceOf(CapabilityCollection::class, $capabilities);
        $this->assertGreaterThan(0, $capabilities->count());

        foreach ($capabilities as $capability) {
            $this->assertInstanceOf(Capability::class, $capability);
        }
    }
}
