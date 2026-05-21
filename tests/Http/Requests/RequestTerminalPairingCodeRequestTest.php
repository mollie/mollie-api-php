<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\RequestTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\TerminalPairingCodeQuery;
use PHPUnit\Framework\TestCase;

class RequestTerminalPairingCodeRequestTest extends TestCase
{
    /** @test */
    public function it_can_request_terminal_pairing_code()
    {
        $client = new MockMollieClient([
            RequestTerminalPairingCodeRequest::class => MockResponse::created('terminal-pairing-code'),
        ]);

        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        /** @var TerminalPairingCode */
        $pairingCode = $client->send($request);

        $this->assertTrue($pairingCode->getResponse()->successful());
        $this->assertEquals(201, $pairingCode->getResponse()->status());
        $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        $this->assertEquals('terminals/pairing-codes', $request->resolveResourcePath());
    }

    /** @test */
    public function it_resolves_payload()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        $this->assertEquals(['profileId' => 'pfl_jA9bC4DkFj3G'], $request->payload()->all());
    }

    /** @test */
    public function it_can_include_qr_code()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G', true);

        $this->assertEquals(TerminalPairingCodeQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }
}
