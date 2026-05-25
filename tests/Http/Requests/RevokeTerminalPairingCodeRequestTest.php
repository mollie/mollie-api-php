<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\RevokeTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
use PHPUnit\Framework\TestCase;

class RevokeTerminalPairingCodeRequestTest extends TestCase
{
    /** @test */
    public function it_can_revoke_terminal_pairing_code()
    {
        $client = new MockMollieClient([
            RevokeTerminalPairingCodeRequest::class => MockResponse::ok('terminal-pairing-code'),
        ]);

        $request = new RevokeTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G');

        /** @var TerminalPairingCode */
        $pairingCode = $client->send($request);

        $this->assertTrue($pairingCode->getResponse()->successful());
        $this->assertInstanceOf(TerminalPairingCode::class, $pairingCode);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new RevokeTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G');

        $this->assertEquals(
            'terminals/pairing-codes/termpc_R7gX5Ea9bC4DkFj3G',
            $request->resolveResourcePath()
        );
    }
}
