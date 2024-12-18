<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSessionsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;

class GetPaginatedSessionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_sessions()
    {
        $client = new MockMollieClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
        ]);

        $request = new GetPaginatedSessionsRequest;

        /** @var SessionCollection */
        $sessions = $client->send($request);

        $this->assertTrue($sessions->getResponse()->successful());

        foreach ($sessions as $session) {
            $this->assertInstanceOf(Session::class, $session);
            $this->assertEquals('session', $session->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_sessions()
    {
        $client = new MockMollieClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'session-list'),
                new MockResponse(200, 'empty-list', 'sessions'),
            ),
        ]);

        $request = (new GetPaginatedSessionsRequest)->useIterator();

        /** @var LazyCollection */
        $sessions = $client->send($request);
        $this->assertTrue($sessions->getResponse()->successful());

        foreach ($sessions as $session) {
            $this->assertInstanceOf(Session::class, $session);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedSessionsRequest;

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
