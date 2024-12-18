<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Resources\MethodCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetEnabledMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_enabled_methods()
    {
        $client = new MockClient([
            GetEnabledMethodsRequest::class => new MockResponse(200, 'method-list'),
        ]);

        $request = new GetEnabledMethodsRequest;

        /** @var MethodCollection */
        $methods = $client->send($request);

        $this->assertTrue($methods->getResponse()->successful());
        $this->assertInstanceOf(MethodCollection::class, $methods);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetEnabledMethodsRequest;

        $this->assertEquals('methods', $request->resolveResourcePath());
    }
}
