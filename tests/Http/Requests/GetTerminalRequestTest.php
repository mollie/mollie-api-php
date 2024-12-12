<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Terminal;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetTerminalRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_terminal()
    {
        $client = new MockClient([
            GetTerminalRequest::class => new MockResponse(200, 'terminal'),
        ]);

        $request = new GetTerminalRequest('term_7MgL4wea');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Terminal */
        $terminal = $response->toResource();

        $this->assertInstanceOf(Terminal::class, $terminal);
        $this->assertEquals('terminal', $terminal->resource);
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
