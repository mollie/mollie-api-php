<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\ClientCollection;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class ClientEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetClientRequest::class => new MockResponse(200, 'client'),
        ]);

        /** @var Client $clientResource */
        $clientResource = $client->clients->get('org_12345678');

        $this->assertClient($clientResource);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedClientRequest::class => new MockResponse(200, 'client-list'),
        ]);

        /** @var ClientCollection $clients */
        $clients = $client->clients->page();

        $this->assertInstanceOf(ClientCollection::class, $clients);
        $this->assertGreaterThan(0, $clients->count());

        foreach ($clients as $clientResource) {
            $this->assertClient($clientResource);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedClientRequest::class => new MockResponse(200, 'client-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'clients'),
        ]);

        foreach ($client->clients->iterator() as $clientResource) {
            $this->assertClient($clientResource);
        }
    }

    protected function assertClient(Client $client)
    {
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('client', $client->resource);
        $this->assertNotEmpty($client->id);
        $this->assertNotEmpty($client->organizationCreatedAt);
        $this->assertNotEmpty($client->_links);
    }
}
