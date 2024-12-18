<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Resources\ClientCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetPaginatedClientRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_clients()
    {
        $client = new MockClient([
            GetPaginatedClientRequest::class => new MockResponse(200, 'client-list'),
        ]);

        $request = new GetPaginatedClientRequest;

        /** @var ClientCollection */
        $clients = $client->send($request);

        $this->assertTrue($clients->getResponse()->successful());
        $this->assertInstanceOf(ClientCollection::class, $clients);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedClientRequest;

        $this->assertEquals('clients', $request->resolveResourcePath());
    }
}
