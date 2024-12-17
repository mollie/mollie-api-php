<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Resources\MethodCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetAllMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_all_methods()
    {
        $client = new MockClient([
            GetAllMethodsRequest::class => new MockResponse(200, 'method-list'),
        ]);

        $request = new GetAllMethodsRequest;

        /** @var MethodCollection */
        $methods = $client->send($request);

        $this->assertTrue($methods->getResponse()->successful());
        $this->assertInstanceOf(MethodCollection::class, $methods);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetAllMethodsRequest;

        $this->assertEquals('methods/all', $request->resolveResourcePath());
    }
}
