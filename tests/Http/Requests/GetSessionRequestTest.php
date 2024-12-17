<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

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

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
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
