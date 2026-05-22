<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
use PHPUnit\Framework\TestCase;

class GetTerminalPairingCodeRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_terminal_pairing_code()
    {
        $client = new MockMollieClient([
            GetTerminalPairingCodeRequest::class => MockResponse::ok('terminal-pairing-code'),
        ]);

        $request = new GetTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G');

        /** @var TerminalPairingCode */
        $pairingCode = $client->send($request);

        $this->assertTrue($pairingCode->getResponse()->successful());
        $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G');

        $this->assertEquals(
            'terminals/pairing-codes/termpc_R7gX5Ea9bC4DkFj3G',
            $request->resolveResourcePath()
        );
    }

    /** @test */
    public function it_includes_qr_code_when_requested()
    {
        $request = new GetTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G', true);

        $this->assertEquals('details.qrCode', $request->query()->get('include'));
    }

    /** @test */
    public function it_does_not_include_qr_code_by_default()
    {
        $request = new GetTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G');

        $this->assertNull($request->query()->get('include'));
    }
}
