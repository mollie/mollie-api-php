<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Resources\Client;
use PHPUnit\Framework\TestCase;

class GetClientRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_client()
    {
        $client = new MockMollieClient([
            GetClientRequest::class => MockResponse::ok('client'),
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
