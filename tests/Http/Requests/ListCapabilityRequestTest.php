<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ListCapabilitiesRequest;
use Mollie\Api\Resources\Capability;
use Mollie\Api\Resources\CapabilityCollection;
use PHPUnit\Framework\TestCase;

class ListCapabilityRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_capabilities()
    {
        $client = new MockMollieClient([
            ListCapabilitiesRequest::class => MockResponse::ok('capability-list'),
        ]);

        $request = new ListCapabilitiesRequest;

        /** @var CapabilityCollection */
        $capabilities = $client->send($request);

        $this->assertTrue($capabilities->getResponse()->successful());
        $this->assertInstanceOf(CapabilityCollection::class, $capabilities);
        $this->assertGreaterThan(0, $capabilities->count());

        foreach ($capabilities as $capability) {
            $this->assertInstanceOf(Capability::class, $capability);
            $this->assertEquals('capability', $capability->resource);
        }
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new ListCapabilitiesRequest;

        $this->assertEquals('capabilities', $request->resolveResourcePath());
    }
}
