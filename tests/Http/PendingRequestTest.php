<?php

namespace Tests\Http;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Repositories\JsonPayloadRepository;
use PHPUnit\Framework\TestCase;

class PendingRequestTest extends TestCase
{
    private MockMollieClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new MockMollieClient;
    }

    /**
     * @test
     */
    public function constructs_url_correctly()
    {
        $request = new DynamicGetRequest('/v2/payments/tr_123');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertStringEndsWith('/v2/payments/tr_123', $pendingRequest->url());

        $request = new DynamicGetRequest('https://example.com/v2/payments/tr_123');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertEquals('https://example.com/v2/payments/tr_123', $pendingRequest->url());
    }

    /**
     * @test
     */
    public function preserves_request_method()
    {
        $request = new DynamicPostRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertEquals('POST', $pendingRequest->method());
    }

    /**
     * @test
     */
    public function can_set_and_get_payload()
    {
        $payload = new JsonPayloadRepository(['amount' => ['value' => '10.00', 'currency' => 'EUR']]);
        $request = new DynamicPostRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $pendingRequest->setPayload($payload);

        $this->assertSame($payload, $pendingRequest->payload());
    }

    /**
     * @test
     */
    public function testmode_is_true_when_connector_has_testmode()
    {
        $this->client->test(true);
        $request = new DynamicGetRequest('/v2/payments');

        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertTrue($pendingRequest->getTestmode());
    }

    /**
     * @test
     */
    public function testmode_is_true_when_request_has_testmode()
    {
        $this->client->test(false);
        $request = new DynamicGetRequest('/v2/payments');
        $request->test(true);

        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertTrue($pendingRequest->getTestmode());
    }

    /**
     * @test
     */
    public function testmode_is_true_when_using_test_api_key()
    {
        $this->client->test(false);
        $this->client->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertTrue($pendingRequest->getTestmode());
    }

    /**
     * @test
     */
    public function testmode_is_false_when_using_live_api_key()
    {
        $this->client->test(false);
        $this->client->setApiKey('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertFalse($pendingRequest->getTestmode());
    }

    /**
     * @test
     */
    public function testmode_is_false_when_using_access_token()
    {
        $this->client->test(false);
        $this->client->setAccessToken('access_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertFalse($pendingRequest->getTestmode());
    }

    /**
     * @test
     */
    public function can_get_request_and_connector()
    {
        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertSame($request, $pendingRequest->getRequest());
        $this->assertSame($this->client, $pendingRequest->getConnector());
    }
}
