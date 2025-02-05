<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class CreateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_session()
    {
        $client = new MockMollieClient([
            DynamicPostRequest::class => MockResponse::created('session'),
        ]);

        $request = new DynamicPostRequest('sessions');

        $request->setHydratableResource(Session::class);

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new DynamicPostRequest('sessions');

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
