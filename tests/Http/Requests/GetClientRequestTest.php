<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\GetClientQuery;
use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Client;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetClientRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_client()
    {
        $client = new MockClient([
            GetClientRequest::class => new MockResponse(200, 'client'),
        ]);

        $request = new GetClientRequest('client_123');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Client::class, $response->toResource());
        $this->assertEquals('client', $response->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetClientRequest('client_123');

        $this->assertEquals('clients/client_123', $request->resolveResourcePath());
    }
}
