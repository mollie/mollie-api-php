<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreateClientLinkPayload;
use Mollie\Api\Http\Payload\Owner;
use Mollie\Api\Http\Payload\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\ClientLink;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateClientLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_client_link()
    {
        $client = new MockClient([
            CreateClientLinkRequest::class => new MockResponse(201, 'client-link'),
        ]);

        $payload = new CreateClientLinkPayload(
            new Owner('test@example.org', 'John', 'Doe'),
            'Test',
            new OwnerAddress('NL')
        );

        $request = new CreateClientLinkRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(ClientLink::class, $response->toResource());
    }
}
