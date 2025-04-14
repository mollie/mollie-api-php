<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicDeleteRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class CancelSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_session()
    {
        $client = new MockMollieClient([
            DynamicDeleteRequest::class => MockResponse::ok('session'),
        ]);

        $sessionId = 'sess_pNxqdWEFws';
        $request = new DynamicDeleteRequest("sessions/{$sessionId}");

        $request->setHydratableResource(Session::class);

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $sessionId = 'sess_pNxqdWEFws';
        $request = new DynamicDeleteRequest("sessions/{$sessionId}");

        $this->assertEquals(
            "sessions/{$sessionId}",
            $request->resolveResourcePath()
        );
    }
}
