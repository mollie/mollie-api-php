<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSessionsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use Mollie\Api\Resources\LazyCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedSessionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_sessions()
    {
        $client = new MockClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
        ]);

        $request = new GetPaginatedSessionsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var SessionCollection */
        $sessions = $response->toResource();
        // Assert response was properly handled
        $this->assertInstanceOf(SessionCollection::class, $sessions);
        $this->assertGreaterThan(0, $sessions->count());

        foreach ($sessions as $session) {
            $this->assertInstanceOf(Session::class, $session);
            $this->assertEquals('session', $session->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_sessions()
    {
        $client = new MockClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'session-list'),
                new MockResponse(200, 'empty-list', 'sessions'),
            ),
        ]);

        $request = (new GetPaginatedSessionsRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $sessions = $response->toResource();

        foreach ($sessions as $session) {
            $this->assertInstanceOf(Session::class, $session);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedSessionsRequest();

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
