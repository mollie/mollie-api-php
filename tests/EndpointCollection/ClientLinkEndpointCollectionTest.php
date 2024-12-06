<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Payload\CreateClientLinkPayload;
use Mollie\Api\Http\Payload\Owner;
use Mollie\Api\Http\Payload\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class ClientLinkEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreateClientLinkRequest::class => new MockResponse(201, 'client-link'),
        ]);

        /** @var ClientLink $clientLink */
        $clientLink = $client->clientLinks->create(new CreateClientLinkPayload(
            new Owner('test@example.com', 'John', 'Doe'),
            'Test Client Link',
            new OwnerAddress('NL'),
        ));

        $this->assertInstanceOf(ClientLink::class, $clientLink);
        $this->assertEquals('client-link', $clientLink->resource);
        $this->assertNotEmpty($clientLink->id);
        $this->assertNotEmpty($clientLink->_links);
    }
}
