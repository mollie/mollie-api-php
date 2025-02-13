<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CancelSessionRequest;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPaginatedRequest;
use Mollie\Api\Http\Requests\DynamicPutRequest;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use PHPUnit\Framework\TestCase;

class SessionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetSessionRequest::class => MockResponse::ok('session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->get('ses_123');

        $this->assertSession($session);
    }

    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateSessionRequest::class => MockResponse::created('session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->create([
            'redirectUrl' => 'https://example.com/redirect',
            'cancelUrl' => 'https://example.com/cancel',
            'amount' => new Money('EUR', '10.00'),
            'description' => 'Test Session',
            'method' => 'ideal',
        ]);

        $this->assertSession($session);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            DynamicPutRequest::class => MockResponse::ok('session'),
        ]);

        /** @var Session $session */
        $session = $client->sessions->update('ses_123', [
            'description' => 'Updated Session',
        ]);

        $this->assertSession($session);
    }

    /** @test */
    public function cancel()
    {
        $client = new MockMollieClient([
            CancelSessionRequest::class => MockResponse::noContent(),
        ]);

        $client->sessions->cancel('ses_123');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            DynamicPaginatedRequest::class => MockResponse::ok('session-list'),
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
        $client = new MockMollieClient([
            DynamicPaginatedRequest::class => MockResponse::ok('session-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'sessions'),
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
        $this->assertNotEmpty($session->status);
        $this->assertNotEmpty($session->amount);
        $this->assertNotEmpty($session->description);
        $this->assertNotEmpty($session->_links);
    }
}
