<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdateSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class UpdateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_session()
    {
        $client = new MockMollieClient([
            UpdateSessionRequest::class => MockResponse::ok('session'),
        ]);

        $request = new UpdateSessionRequest('ses_LQNz4v4Qvk', [
            'status' => 'completed',
            'metadata' => [
                'order_id' => '12345',
            ],
        ]);

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());

        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('session', $session->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $sessionId = 'ses_LQNz4v4Qvk';
        $request = new UpdateSessionRequest($sessionId, []);

        $this->assertEquals("sessions/{$sessionId}", $request->resolveResourcePath());
    }
}
