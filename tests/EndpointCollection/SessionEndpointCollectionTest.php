<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Resources\Session;
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
            'amount' => new Money('EUR', '10.00'),
            'description' => 'Test Session',
            'redirectUrl' => 'https://example.com/redirect',
            'lines' => [
                [
                    'description' => 'Product A',
                    'quantity' => 1,
                    'unitPrice' => [
                        'currency' => 'EUR',
                        'value' => '10.00',
                    ],
                    'totalAmount' => [
                        'currency' => 'EUR',
                        'value' => '10.00',
                    ],
                ],
            ],
        ]);

        $this->assertSession($session);
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
