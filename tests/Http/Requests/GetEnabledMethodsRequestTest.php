<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\MethodCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetEnabledMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_enabled_methods()
    {
        $client = new MockClient([
            GetEnabledMethodsRequest::class => new MockResponse(200, 'method-list'),
        ]);

        $request = new GetEnabledMethodsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(MethodCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetEnabledMethodsRequest;

        $this->assertEquals('methods', $request->resolveResourcePath());
    }
}
