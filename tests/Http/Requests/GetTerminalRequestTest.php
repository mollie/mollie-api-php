<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\Terminal;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetTerminalRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_terminal()
    {
        $client = new MockClient([
            GetTerminalRequest::class => new MockResponse(200, 'terminal'),
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
