<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Session;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

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

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Session::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSessionRequest(new AnyData(['foo' => 'bar']), new AnyData(['baz' => 'qux']));

        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}
