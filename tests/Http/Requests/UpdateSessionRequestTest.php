<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\UpdateSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class UpdateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_session()
    {
        $client = new MockClient([
            UpdateSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        $payload = new AnyData([
            'status' => 'completed',
            'metadata' => [
                'order_id' => '12345',
            ],
        ]);

        $request = new UpdateSessionRequest('ses_LQNz4v4Qvk', $payload);

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
        $request = new UpdateSessionRequest($sessionId, new AnyData);

        $this->assertEquals("sessions/{$sessionId}", $request->resolveResourcePath());
    }
}
