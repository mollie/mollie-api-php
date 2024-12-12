<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\CancelSessionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Session;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CancelSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_session()
    {
        $client = new MockClient([
            CancelSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        $sessionId = 'sess_pNxqdWEFws';
        $request = new CancelSessionRequest($sessionId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Session::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $sessionId = 'sess_pNxqdWEFws';
        $request = new CancelSessionRequest($sessionId);

        $this->assertEquals(
            "sessions/{$sessionId}",
            $request->resolveResourcePath()
        );
    }
}
