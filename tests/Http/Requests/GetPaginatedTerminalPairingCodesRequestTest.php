<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalPairingCodesRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedTerminalPairingCodesRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_terminal_pairing_codes()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalPairingCodesRequest::class => MockResponse::ok('terminal-pairing-code-list'),
        ]);

        $request = new GetPaginatedTerminalPairingCodesRequest;

        /** @var TerminalPairingCodeCollection */
        $pairingCodes = $client->send($request);

        $this->assertTrue($pairingCodes->getResponse()->successful());

        foreach ($pairingCodes as $pairingCode) {
            $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
            $this->assertEquals('terminal-pairing-code', $pairingCode->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_terminal_pairing_codes()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalPairingCodesRequest::class => MockResponse::ok('terminal-pairing-code-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('terminal-pairing-code-list'),
                MockResponse::ok('empty-list', 'terminal-pairing-codes'),
            ),
        ]);

        $request = (new GetPaginatedTerminalPairingCodesRequest)->useIterator();

        /** @var LazyCollection */
        $pairingCodes = $client->send($request);
        $this->assertTrue($pairingCodes->getResponse()->successful());

        foreach ($pairingCodes as $pairingCode) {
            $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest;

        $this->assertEquals('terminals/pairing-codes', $request->resolveResourcePath());
    }

    /** @test */
    public function it_resolves_query_parameters()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest('termpc_R7gX5Ea9bC4DkFj3G', 25, 'asc', 'pfl_jA9bC4DkFj3G');

        $this->assertEquals([
            'from' => 'termpc_R7gX5Ea9bC4DkFj3G',
            'limit' => 25,
            'sort' => 'asc',
            'profileId' => 'pfl_jA9bC4DkFj3G',
        ], $request->query()->all());
    }
}
