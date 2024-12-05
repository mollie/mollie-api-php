<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\ClientCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedClientRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_clients()
    {
        $client = new MockClient([
            GetPaginatedClientRequest::class => new MockResponse(200, 'client-list'),
        ]);

        $request = new GetPaginatedClientRequest();

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(ClientCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedClientRequest();

        $this->assertEquals('clients', $request->resolveResourcePath());
    }
}
