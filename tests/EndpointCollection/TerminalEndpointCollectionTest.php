<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use PHPUnit\Framework\TestCase;

class TerminalEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetTerminalRequest::class => MockResponse::ok('terminal'),
        ]);

        /** @var Terminal $terminal */
        $terminal = $client->terminals->get('term_7jKQR2wmKx');

        $this->assertTerminal($terminal);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalsRequest::class => MockResponse::ok('terminal-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'terminals'),
        ]);

        /** @var TerminalCollection $terminals */
        $terminals = $client->terminals->page();

        $this->assertInstanceOf(TerminalCollection::class, $terminals);
        $this->assertGreaterThan(0, $terminals->count());

        foreach ($terminals as $terminal) {
            $this->assertTerminal($terminal);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalsRequest::class => MockResponse::ok('terminal-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'terminals'),
        ]);

        foreach ($client->terminals->iterator() as $terminal) {
            $this->assertTerminal($terminal);
        }
    }

    protected function assertTerminal(Terminal $terminal)
    {
        $this->assertInstanceOf(Terminal::class, $terminal);
        $this->assertEquals('terminal', $terminal->resource);
        $this->assertNotEmpty($terminal->id);
        $this->assertNotEmpty($terminal->profileId);
        $this->assertNotEmpty($terminal->status);
        $this->assertNotEmpty($terminal->brand);
        $this->assertNotEmpty($terminal->model);
        $this->assertNotEmpty($terminal->serialNumber);
        $this->assertNotEmpty($terminal->currency);
        $this->assertNotEmpty($terminal->createdAt);
        $this->assertNotEmpty($terminal->_links);
    }
}
