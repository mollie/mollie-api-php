<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class GetSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_session()
    {
        $client = new MockMollieClient([
            GetSessionRequest::class => MockResponse::ok('session'),
        ]);

        $request = new GetSessionRequest('ses_LQNz4v4Qvk');

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetSessionRequest('ses_LQNz4v4Qvk');

        $this->assertEquals(
            'sessions/ses_LQNz4v4Qvk',
            $request->resolveResourcePath()
        );
    }
}
