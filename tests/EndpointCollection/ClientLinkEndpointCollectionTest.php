<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class ClientLinkEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreateClientLinkRequest::class => new MockResponse(201, 'client-link'),
        ]);

        /** @var ClientLink $clientLink */
        $clientLink = $client->clientLinks->create([
            'ownerId' => 'org_12345678',
            'name' => 'Test Client Link',
        ]);

        $this->assertInstanceOf(ClientLink::class, $clientLink);
        $this->assertEquals('client-link', $clientLink->resource);
        $this->assertNotEmpty($clientLink->id);
        $this->assertNotEmpty($clientLink->_links);
    }
}
