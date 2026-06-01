<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\RequestTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
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
        $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        $this->assertEquals('terminals/pairing-codes', $request->resolveResourcePath());
    }

    /** @test */
    public function it_includes_profile_id_in_payload()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        $this->assertEquals('pfl_jA9bC4DkFj3G', $request->payload()->get('profileId'));
    }

    /** @test */
    public function it_includes_qr_code_when_requested()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G', true);

        $this->assertEquals('details.qrCode', $request->query()->get('include'));
    }

    /** @test */
    public function it_does_not_include_qr_code_by_default()
    {
        $request = new RequestTerminalPairingCodeRequest('pfl_jA9bC4DkFj3G');

        $this->assertNull($request->query()->get('include'));
    }
}
