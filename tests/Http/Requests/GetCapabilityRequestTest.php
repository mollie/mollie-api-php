<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetCapabilityRequest;
use Mollie\Api\Resources\Capability;
use PHPUnit\Framework\TestCase;

class GetCapabilityRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_capability()
    {
        $client = new MockMollieClient([
            GetCapabilityRequest::class => MockResponse::ok('capability'),
        ]);

        $request = new GetCapabilityRequest('payments');

        /** @var Capability */
        $capability = $client->send($request);

        $this->assertTrue($capability->getResponse()->successful());
        $this->assertInstanceOf(Capability::class, $capability);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetCapabilityRequest('payments');

        $this->assertEquals(
            'capabilities/payments',
            $request->resolveResourcePath()
        );
    }
}
