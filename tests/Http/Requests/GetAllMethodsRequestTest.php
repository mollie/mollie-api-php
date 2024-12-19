<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Resources\MethodCollection;
use PHPUnit\Framework\TestCase;

class GetAllMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_all_methods()
    {
        $client = new MockMollieClient([
            GetAllMethodsRequest::class => MockResponse::ok('method-list'),
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
