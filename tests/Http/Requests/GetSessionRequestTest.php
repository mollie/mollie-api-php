<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Session;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_session()
    {
        $client = new MockClient([
            GetSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        $query = new AnyData([
            'testmode' => true,
        ]);

        $request = new GetSessionRequest('ses_LQNz4v4Qvk', $query);

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
        $query = new AnyData([
            'testmode' => true,
        ]);

        $request = new GetSessionRequest('ses_LQNz4v4Qvk', $query);

        $this->assertEquals(
            'sessions/ses_LQNz4v4Qvk',
            $request->resolveResourcePath()
        );
    }
}
