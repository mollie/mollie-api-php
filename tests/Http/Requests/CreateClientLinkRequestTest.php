<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Http\Data\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;
use PHPUnit\Framework\TestCase;

class CreateClientLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_client_link()
    {
        $client = new MockMollieClient([
            CreateClientLinkRequest::class => MockResponse::created('client-link'),
        ]);

        $request = new CreateClientLinkRequest(
            new Owner('test@example.org', 'John', 'Doe'),
            'Test',
            new OwnerAddress('NL')
        );

        /** @var ClientLink */
        $clientLink = $client->send($request);

        $this->assertTrue($clientLink->getResponse()->successful());
        $this->assertInstanceOf(ClientLink::class, $clientLink);
    }
}
