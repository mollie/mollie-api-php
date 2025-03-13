<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\Terminal;
use PHPUnit\Framework\TestCase;

class GetTerminalRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_terminal()
    {
        $client = new MockMollieClient([
            GetTerminalRequest::class => MockResponse::ok('terminal'),
        ]);

        $request = new GetTerminalRequest('term_7MgL4wea');

        /** @var Terminal */
        $terminal = $client->send($request);

        $this->assertTrue($terminal->getResponse()->successful());
        $this->assertInstanceOf(Terminal::class, $terminal);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetTerminalRequest('term_7MgL4wea');

        $this->assertEquals(
            'terminals/term_7MgL4wea',
            $request->resolveResourcePath()
        );
    }
}
