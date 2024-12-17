<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class CreateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_session()
    {
        $client = new MockClient([
            CreateSessionRequest::class => new MockResponse(201, 'session'),
        ]);

        $request = new CreateSessionRequest(
            new AnyData(['foo' => 'bar']),
            new AnyData(['baz' => 'qux'])
        );

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSessionRequest(new AnyData(['foo' => 'bar']), new AnyData(['baz' => 'qux']));

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
