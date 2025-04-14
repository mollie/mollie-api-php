<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedTerminalsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_terminals()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalsRequest::class => MockResponse::ok('terminal-list'),
        ]);

        $request = new GetPaginatedTerminalsRequest;

        /** @var TerminalCollection */
        $terminals = $client->send($request);

        $this->assertTrue($terminals->getResponse()->successful());

        foreach ($terminals as $terminal) {
            $this->assertInstanceOf(Terminal::class, $terminal);
            $this->assertEquals('terminal', $terminal->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_terminals()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalsRequest::class => MockResponse::ok('terminal-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('terminal-list'),
                MockResponse::ok('empty-list', 'terminals'),
            ),
        ]);

        $request = (new GetPaginatedTerminalsRequest)->useIterator();

        /** @var LazyCollection */
        $terminals = $client->send($request);
        $this->assertTrue($terminals->getResponse()->successful());

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
