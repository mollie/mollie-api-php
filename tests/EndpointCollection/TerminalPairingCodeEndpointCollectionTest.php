<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalPairingCodesRequest;
use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Http\Requests\RequestTerminalPairingCodeRequest;
use Mollie\Api\Http\Requests\RevokeTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
use PHPUnit\Framework\TestCase;

class TerminalPairingCodeEndpointCollectionTest extends TestCase
{
    /** @test */
    public function request()
    {
        $client = new MockMollieClient([
            RequestTerminalPairingCodeRequest::class => MockResponse::created('terminal-pairing-code'),
        ]);

        /** @var TerminalPairingCode $pairingCode */
        $pairingCode = $client->terminalPairingCodes->request('pfl_jA9bC4DkFj3G');

        $this->assertPairingCode($pairingCode);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetTerminalPairingCodeRequest::class => MockResponse::ok('terminal-pairing-code'),
        ]);

        /** @var TerminalPairingCode $pairingCode */
        $pairingCode = $client->terminalPairingCodes->get('termpc_R7gX5Ea9bC4DkFj3G');

        $this->assertPairingCode($pairingCode);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalPairingCodesRequest::class => MockResponse::ok('terminal-pairing-code-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'terminal-pairing-codes'),
        ]);

        /** @var TerminalPairingCodeCollection $pairingCodes */
        $pairingCodes = $client->terminalPairingCodes->page();

        $this->assertInstanceOf(TerminalPairingCodeCollection::class, $pairingCodes);
        $this->assertGreaterThan(0, $pairingCodes->count());

        foreach ($pairingCodes as $pairingCode) {
            $this->assertPairingCode($pairingCode);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalPairingCodesRequest::class => MockResponse::ok('terminal-pairing-code-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'terminal-pairing-codes'),
        ]);

        foreach ($client->terminalPairingCodes->iterator() as $pairingCode) {
            $this->assertPairingCode($pairingCode);
        }
    }

    /** @test */
    public function revoke()
    {
        $client = new MockMollieClient([
            RevokeTerminalPairingCodeRequest::class => MockResponse::ok('terminal-pairing-code'),
        ]);

        /** @var TerminalPairingCode $pairingCode */
        $pairingCode = $client->terminalPairingCodes->revoke('termpc_R7gX5Ea9bC4DkFj3G');

        $this->assertPairingCode($pairingCode);
    }

    protected function assertPairingCode(TerminalPairingCode $pairingCode): void
    {
        $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
        $this->assertEquals('terminal-pairing-code', $pairingCode->resource);
        $this->assertNotEmpty($pairingCode->id);
        $this->assertNotEmpty($pairingCode->code);
        $this->assertNotEmpty($pairingCode->profileId);
        $this->assertNotEmpty($pairingCode->status);
        $this->assertNotEmpty($pairingCode->expiresAt);
        $this->assertNotEmpty($pairingCode->createdAt);
        $this->assertNotEmpty($pairingCode->_links);
    }
}
