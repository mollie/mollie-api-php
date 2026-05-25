<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedTerminalPairingCodesRequest;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedTerminalPairingCodesRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_terminal_pairing_codes()
    {
        $client = new MockMollieClient([
            GetPaginatedTerminalPairingCodesRequest::class => MockResponse::ok('terminal-pairing-code-list'),
        ]);

        $request = new GetPaginatedTerminalPairingCodesRequest();

        /** @var TerminalPairingCodeCollection */
        $pairingCodes = $client->send($request);

        $this->assertTrue($pairingCodes->getResponse()->successful());
        $this->assertInstanceOf(TerminalPairingCodeCollection::class, $pairingCodes);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest();

        $this->assertEquals('terminals/pairing-codes', $request->resolveResourcePath());
    }

    /** @test */
    public function it_passes_profile_id_filter()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest(null, null, 'pfl_jA9bC4DkFj3G');

        $this->assertEquals('pfl_jA9bC4DkFj3G', $request->query()->get('profileId'));
    }

    /** @test */
    public function it_passes_pagination_params()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest(
            'termpc_R7gX5Ea9bC4DkFj3G',
            10
        );

        $this->assertEquals('termpc_R7gX5Ea9bC4DkFj3G', $request->query()->get('from'));
        $this->assertEquals(10, $request->query()->get('limit'));
    }

    /** @test */
    public function it_passes_sort_param()
    {
        $request = new GetPaginatedTerminalPairingCodesRequest(null, null, null, 'desc');

        $this->assertEquals('desc', $request->query()->get('sort'));
    }
}
