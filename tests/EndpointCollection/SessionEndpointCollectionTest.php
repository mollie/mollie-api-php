<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Payload\AnyPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Query\AnyQuery;
use Mollie\Api\Http\Requests\CancelSessionRequest;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSessionsRequest;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Http\Requests\UpdateSessionRequest;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class SessionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->get('ses_123', new AnyQuery(['include' => 'details']));

        $this->assertSession($session);
    }

    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreateSessionRequest::class => new MockResponse(201, 'session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->create(new AnyPayload([
            'amount' => new Money('EUR', '10.00'),
            'description' => 'Test Session',
        ]));

        $this->assertSession($session);
    }

    /** @test */
    public function update()
    {
        $client = new MockClient([
            UpdateSessionRequest::class => new MockResponse(200, 'session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->update('ses_123', new AnyPayload([
            'description' => 'Updated Session',
        ]));

        $this->assertSession($session);
    }

    /** @test */
    public function cancel()
    {
        $client = new MockClient([
            CancelSessionRequest::class => new MockResponse(204),
        ]);

        $client->sessions->cancel('ses_123');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
        ]);

        /** @var SessionCollection $sessions */
        $sessions = $client->sessions->page();

        $this->assertInstanceOf(SessionCollection::class, $sessions);
        $this->assertGreaterThan(0, $sessions->count());

        foreach ($sessions as $session) {
            $this->assertSession($session);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockClient([
            GetPaginatedSessionsRequest::class => new MockResponse(200, 'session-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'sessions'),
        ]);

        foreach ($client->sessions->iterator() as $session) {
            $this->assertSession($session);
        }
    }

    protected function assertSession(Session $session)
    {
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('session', $session->resource);
        $this->assertNotEmpty($session->id);
        $this->assertNotEmpty($session->mode);
        $this->assertNotEmpty($session->createdAt);
        $this->assertNotEmpty($session->status);
        $this->assertNotEmpty($session->amount);
        $this->assertNotEmpty($session->description);
        $this->assertNotEmpty($session->_links);
    }
}
