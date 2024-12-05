<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\GetAllMethodsQuery;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\MethodCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetAllMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_all_methods()
    {
        $client = new MockClient([
            GetAllMethodsRequest::class => new MockResponse(200, 'method-list'),
        ]);

        $request = new GetAllMethodsRequest();

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(MethodCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetAllMethodsRequest();

        $this->assertEquals('methods/all', $request->resolveResourcePath());
    }
}
