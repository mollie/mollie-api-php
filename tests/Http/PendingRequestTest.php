<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Http\Requests\DynamicPutRequest;
use Mollie\Api\Repositories\JsonPayloadRepository;
use Mollie\Api\Utils\Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PendingRequestTest extends TestCase
{
    private MockMollieClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new MockMollieClient;
    }

    #[Test]
    public function constructs_url_correctly()
    {
        $request = new DynamicGetRequest('/v2/payments/tr_123');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertStringEndsWith('/v2/payments/tr_123', $pendingRequest->url());

        $request = new DynamicGetRequest('https://example.com/v2/payments/tr_123');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertEquals('https://example.com/v2/payments/tr_123', $pendingRequest->url());
    }

    #[Test]
    public function preserves_request_method()
    {
        $request = new DynamicPostRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertEquals('POST', $pendingRequest->method());
    }

    #[Test]
    public function can_set_and_get_payload()
    {
        $payload = new JsonPayloadRepository(['amount' => ['value' => '10.00', 'currency' => 'EUR']]);
        $request = new DynamicPostRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $pendingRequest->setPayload($payload);

        $this->assertSame($payload, $pendingRequest->payload());
    }

    #[Test]
    #[DataProvider('effectiveTestmodeProvider')]
    public function resolves_effective_testmode_and_outbound_query(
        ?string $apiKey,
        bool $connectorTestmode,
        bool $requestTestmode,
        bool $expectedTestmode,
        bool $expectedExplicitTestmode,
    ) {
        if ($apiKey !== null) {
            $this->client->setApiKey($apiKey);
        }

        $this->client->test($connectorTestmode);

        $query = $apiKey === null ? [] : ['testmode' => true];
        $request = (new DynamicGetRequest('/v2/payments', $query))->test($requestTestmode);
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertSame($expectedTestmode, $pendingRequest->getTestmode());
        $this->assertSame($expectedExplicitTestmode, $pendingRequest->query()->has('testmode'));

        if ($expectedExplicitTestmode) {
            $this->assertTrue($pendingRequest->query()->get('testmode'));
        }
    }

    public static function effectiveTestmodeProvider(): array
    {
        return [
            'test API key with false flags' => ['test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM', false, false, true, false],
            'test API key with true connector and request flags' => ['test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM', true, true, true, false],
            'live API key with connector flag' => ['live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM', true, false, false, false],
            'live API key with request flag' => ['live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM', false, true, false, false],
            'access token with false flags' => [null, false, false, false, false],
            'access token with connector flag' => [null, true, false, true, true],
            'access token with request flag' => [null, false, true, true, true],
        ];
    }

    #[Test]
    #[DataProvider('transportTestmodeProvider')]
    public function resolves_effective_testmode_from_final_outbound_query(
        string $url,
        array $query,
        bool $expectedTestmode,
        bool $expectedOutboundTestmode,
    ) {
        $request = new DynamicGetRequest($url, $query);
        $pendingRequest = new PendingRequest($this->client, $request);

        $outboundQuery = parse_url((string) $pendingRequest->getUri(), PHP_URL_QUERY);
        $this->assertIsString($outboundQuery);
        $parsedQuery = Url::parseQuery($outboundQuery);

        $this->assertSame($expectedTestmode, $pendingRequest->getTestmode());
        $this->assertSame(
            $expectedOutboundTestmode,
            filter_var($parsedQuery['testmode'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true,
        );
    }

    public static function transportTestmodeProvider(): array
    {
        return [
            'absolute continuation true' => [
                'https://api.mollie.com/v2/payments?from=tr_page2&testmode=true',
                [],
                true,
                true,
            ],
            'absolute continuation false' => [
                'https://api.mollie.com/v2/payments?from=tr_page2&testmode=false',
                [],
                false,
                false,
            ],
            'pending query true' => ['/v2/payments', ['testmode' => 'true'], true, true],
            'pending query overrides embedded query' => [
                'https://api.mollie.com/v2/payments?testmode=true',
                ['testmode' => 'false'],
                false,
                false,
            ],
        ];
    }

    #[Test]
    #[DataProvider('payloadTestmodeProvider')]
    public function safely_resolves_effective_testmode_from_selected_payload(mixed $value, bool $expectedTestmode)
    {
        $pendingRequest = new PendingRequest(
            $this->client,
            new DynamicPutRequest('/v2/payments/tr_123', ['testmode' => $value]),
        );

        $payload = json_decode((string) $pendingRequest->createPsrRequest()->getBody(), true);

        $this->assertSame($expectedTestmode, $pendingRequest->getTestmode());
        $this->assertSame($expectedTestmode, $payload['testmode'] === true);
    }

    public static function payloadTestmodeProvider(): array
    {
        return [
            'string true' => ['true', true],
            'string false' => ['false', false],
            'malformed scalar' => ['not-a-boolean', false],
            'non-scalar' => [['true'], false],
        ];
    }

    #[Test]
    public function effective_testmode_remains_aligned_with_outbound_query_after_flag_mutation()
    {
        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->client->test(true);
        $request->test(true);

        $this->assertFalse($pendingRequest->getTestmode());
        $this->assertFalse($pendingRequest->query()->has('testmode'));

        $request = (new DynamicGetRequest('/v2/payments'))->test(true);
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->client->test(false);
        $request->test(false);

        $this->assertTrue($pendingRequest->getTestmode());
        $this->assertTrue($pendingRequest->query()->get('testmode'));
    }

    #[Test]
    public function effective_testmode_remains_stable_after_api_key_mutation()
    {
        $this->client->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $pendingRequest = new PendingRequest(
            $this->client,
            new DynamicGetRequest('/v2/payments', ['testmode' => true]),
        );

        $this->client->setApiKey('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $this->assertTrue($pendingRequest->getTestmode());
        $this->assertFalse($pendingRequest->query()->has('testmode'));
    }

    #[Test]
    public function api_key_authentication_removes_explicit_testmode_from_payload()
    {
        $this->client->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $request = new DynamicPutRequest('/v2/payments/tr_123', ['testmode' => true]);
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertTrue($pendingRequest->getTestmode());

        $payload = $pendingRequest->payload();
        $this->assertNotNull($payload);
        $this->assertFalse($payload->has('testmode'));
    }

    #[Test]
    public function can_get_request_and_connector()
    {
        $request = new DynamicGetRequest('/v2/payments');
        $pendingRequest = new PendingRequest($this->client, $request);

        $this->assertSame($request, $pendingRequest->getRequest());
        $this->assertSame($this->client, $pendingRequest->getConnector());
    }
}
