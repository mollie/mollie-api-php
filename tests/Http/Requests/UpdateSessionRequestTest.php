<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\AnyPayload;
use Mollie\Api\Http\Requests\UpdateSessionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Session;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_session()
    {
        $client = new MockClient([
            UpdateSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        $payload = new AnyPayload([
            'status' => 'completed',
            'metadata' => [
                'order_id' => '12345',
            ],
        ]);

        $request = new UpdateSessionRequest('ses_LQNz4v4Qvk', $payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Session */
        $session = $response->toResource();

        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('session', $session->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $sessionId = 'ses_LQNz4v4Qvk';
        $request = new UpdateSessionRequest($sessionId, new AnyPayload());

        $this->assertEquals("sessions/{$sessionId}", $request->resolveResourcePath());
    }
}
