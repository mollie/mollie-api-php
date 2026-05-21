<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\TerminalPairingCodeQuery;
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
    public function it_can_include_qr_code()
    {
        $request = new GetTerminalPairingCodeRequest('termpc_R7gX5Ea9bC4DkFj3G', true);

        $this->assertEquals(TerminalPairingCodeQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }
}
