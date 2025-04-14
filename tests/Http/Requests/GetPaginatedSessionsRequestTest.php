<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPaginatedRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSessionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_sessions()
    {
        $client = new MockMollieClient([
            DynamicPaginatedRequest::class => MockResponse::ok('session-list'),
        ]);

        $request = new DynamicPaginatedRequest('sessions');

        $request->setHydratableResource(SessionCollection::class);

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
            DynamicPaginatedRequest::class => MockResponse::ok('session-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('session-list'),
                MockResponse::ok('empty-list', 'sessions'),
            ),
        ]);

        $request = (new DynamicPaginatedRequest('sessions'))->useIterator();

        $request->setHydratableResource(SessionCollection::class);

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
        $request = new DynamicPaginatedRequest('sessions');

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
