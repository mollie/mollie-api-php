<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class CreateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_session()
    {
        $client = new MockMollieClient([
            CreateSessionRequest::class => MockResponse::created('session'),
        ]);

        $request = new CreateSessionRequest();

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSessionRequest();

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
