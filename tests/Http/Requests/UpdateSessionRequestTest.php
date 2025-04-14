<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicPutRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class UpdateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_session()
    {
        $client = new MockMollieClient([
            DynamicPutRequest::class => MockResponse::ok('session'),
        ]);

        $request = new DynamicPutRequest('ses_LQNz4v4Qvk', [
            'status' => 'completed',
            'metadata' => [
                'order_id' => '12345',
            ],
        ]);

        $request->setHydratableResource(Session::class);

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
        $request = new DynamicPutRequest("sessions/{$sessionId}");

        $this->assertEquals("sessions/{$sessionId}", $request->resolveResourcePath());
    }
}
