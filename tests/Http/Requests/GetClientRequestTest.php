<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Resources\Client;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetClientRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_client()
    {
        $client = new MockClient([
            GetClientRequest::class => new MockResponse(200, 'client'),
        ]);

        $request = new GetClientRequest('client_123');

        /** @var Client */
        $client = $client->send($request);

        $this->assertTrue($client->getResponse()->successful());
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('client', $client->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetClientRequest('client_123');

        $this->assertEquals('clients/client_123', $request->resolveResourcePath());
    }
}
