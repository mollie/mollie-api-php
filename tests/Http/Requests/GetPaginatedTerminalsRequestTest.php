<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedTerminalsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_terminals()
    {
        $client = new MockClient([
            GetPaginatedTerminalsRequest::class => new MockResponse(200, 'terminal-list'),
        ]);

        $request = new GetPaginatedTerminalsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var TerminalCollection */
        $terminals = $response->toResource();
        // Assert response was properly handled
        $this->assertInstanceOf(TerminalCollection::class, $terminals);
        $this->assertGreaterThan(0, $terminals->count());

        foreach ($terminals as $terminal) {
            $this->assertInstanceOf(Terminal::class, $terminal);
            $this->assertEquals('terminal', $terminal->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_terminals()
    {
        $client = new MockClient([
            GetPaginatedTerminalsRequest::class => new MockResponse(200, 'terminal-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'terminal-list'),
                new MockResponse(200, 'empty-list', 'terminals'),
            ),
        ]);

        $request = (new GetPaginatedTerminalsRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $terminals = $response->toResource();

        foreach ($terminals as $terminal) {
            $this->assertInstanceOf(Terminal::class, $terminal);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedTerminalsRequest;

        $this->assertEquals('terminals', $request->resolveResourcePath());
    }
}
