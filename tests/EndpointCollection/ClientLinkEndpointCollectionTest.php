<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Http\Data\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;
use PHPUnit\Framework\TestCase;

class ClientLinkEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateClientLinkRequest::class => MockResponse::created('client-link'),
        ]);

        /** @var ClientLink $clientLink */
        $clientLink = $client->clientLinks->create([
            'owner' => new Owner('test@example.com', 'John', 'Doe'),
            'name' => 'Test Client Link',
            'address' => new OwnerAddress('NL'),
        ]);

        $this->assertInstanceOf(ClientLink::class, $clientLink);
        $this->assertEquals('client-link', $clientLink->resource);
        $this->assertNotEmpty($clientLink->id);
        $this->assertNotEmpty($clientLink->_links);
    }
}
