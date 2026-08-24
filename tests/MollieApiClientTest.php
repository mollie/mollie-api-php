<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\EndpointCollection\PaymentEndpointCollection;
use Mollie\Api\EndpointCollection\TerminalPairingCodeEndpointCollection;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\InvalidAuthenticationException;
use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Middleware\ApplyIdempotencyKey;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Idempotency\FakeIdempotencyKeyGenerator;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\ResourceWrapper;
use Mollie\Api\Resources\WrapperResource;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Debugger;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Requests\DynamicDeleteRequest;
use Tests\Fixtures\Requests\DynamicGetRequest;

class MollieApiClientTest extends TestCase
{
    #[Test]
    public function send_returns_body_as_object()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        /** @var AnyResource $response */
        $response = $client->send(new DynamicGetRequest(''));

        $this->assertEquals(
            ['resource' => 'payment'],
            $response->toArray()
        );
    }

    #[Test]
    public function send_creates_api_exception_correctly()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::unprocessableEntity('Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?', 'recurringType'),
        ]);

        try {
            $client->send(new DynamicGetRequest(''));
        } catch (ValidationException $e) {
            $this->assertEquals('recurringType', $e->getField());
            $this->assertNotEmpty($e->getDocumentationUrl());

            throw $e;
        }
    }

    #[Test]
    public function can_be_serialized_and_unserialized()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $client->setApiEndpoint('https://mymollieproxy.local');
        $serialized = \serialize($client);

        $this->assertStringNotContainsString('test_foobarfoobarfoobarfoobarfoobar', $serialized, 'API key should not be in serialized data or it will end up in caches.');

        /** @var MollieApiClient $client_copy */
        $client_copy = unserialize($serialized);

        $this->assertEmpty($client_copy->getAuthenticator(), 'Authenticator should not have been remembered');
        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $client_copy->getHttpClient(), 'A Guzzle client should have been set.');
        $this->assertEquals('https://mymollieproxy.local', $client_copy->getApiEndpoint(), 'The API endpoint should be remembered');

        $this->assertNotEmpty($client_copy->customerPayments);
        $this->assertNotEmpty($client_copy->payments);
        $this->assertNotEmpty($client_copy->payouts);
        $this->assertNotEmpty($client_copy->methods);
        $this->assertNotEmpty($client_copy->terminalPairingCodes);
    }

    #[Test]
    public function constructor_bypass_client_resolves_endpoints_in_a_cold_process()
    {
        $script = <<<'PHP'
require 'vendor/autoload.php';

$client = (new ReflectionClass(Mollie\Api\MollieApiClient::class))->newInstanceWithoutConstructor();

echo json_encode([
    get_class($client->payments),
    get_class($client->terminalPairingCodes),
], JSON_THROW_ON_ERROR);
PHP;

        $process = proc_open(
            [PHP_BINARY, '-r', $script],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__),
        );

        if (! is_resource($process)) {
            $this->fail('Unable to start a fresh PHP process.');
        }

        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, $errorOutput);
        $this->assertSame([
            PaymentEndpointCollection::class,
            TerminalPairingCodeEndpointCollection::class,
        ], json_decode($output, true, flags: JSON_THROW_ON_ERROR));
    }

    #[Test]
    public function unknown_endpoint_throws_the_same_exception()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Undefined endpoint: unknown');

        $client->__get('unknown');
    }

    #[Test]
    public function subclass_can_override_the_endpoint_map()
    {
        $client = new class($this->createMock(Client::class)) extends MollieApiClient {
            protected const ENDPOINTS = [
                ...parent::ENDPOINTS,
                'customPayments' => PaymentEndpointCollection::class,
            ];
        };

        $this->assertInstanceOf(PaymentEndpointCollection::class, $client->payments);
        $this->assertInstanceOf(PaymentEndpointCollection::class, $client->__get('customPayments'));
    }

    #[Test]
    public function set_token_uses_access_token_authenticator_for_access_tokens()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $client->setToken('access_xxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $this->assertInstanceOf(AccessTokenAuthenticator::class, $client->getAuthenticator());
    }

    #[Test]
    public function set_token_uses_api_key_authenticator_for_test_keys()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $client->setToken('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $client->getAuthenticator());
    }

    #[Test]
    public function set_token_uses_api_key_authenticator_for_live_keys()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $client->setToken('live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $client->getAuthenticator());
    }

    #[Test]
    public function set_token_throws_for_invalid_tokens()
    {
        $client = new MollieApiClient($this->createMock(Client::class));

        $this->expectException(InvalidAuthenticationException::class);

        $client->setToken('invalid_token');
    }

    /**
     * This test verifies that our request headers are correctly sent to Mollie.
     * If these are broken, it could be that some payments do not work.
     *
     * @throws ApiException
     */
    #[Test]
    public function correct_request_headers()
    {
        $client = new MockMollieClient([
            CreatePaymentRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $response = $client->send(new CreatePaymentRequest(
            'test',
            new Money('EUR', '100.00'),
        ));

        $usedHeaders = $response->getPendingRequest()->headers()->all();

        // these change through environments
        // just make sure its existing
        $this->assertArrayHasKey('User-Agent', $usedHeaders);
        $this->assertArrayHasKey('X-Mollie-Client-Info', $usedHeaders);

        // these should be exactly the expected values
        $this->assertEquals('Bearer test_foobarfoobarfoobarfoobarfoobar', $usedHeaders['Authorization']);
        $this->assertEquals('application/json', $usedHeaders['Accept']);
    }

    /**
     * This test verifies that we do not add a Content-Type request header
     * if we do not send a BODY (skipping argument).
     * In this case it has to be skipped.
     *
     * @throws ApiException
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatformException
     * @throws \Mollie\Api\Exceptions\UnrecognizedClientException
     */
    #[Test]
    public function no_content_type_without_provided_body()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::noContent(),
        ]);

        /** @var Response $response */
        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has('Content-Type'));
    }

    #[Test]
    public function no_idempotency_is_set_if_no_key_nor_generator_are_set()
    {
        $client = new MockMollieClient([
            DynamicDeleteRequest::class => MockResponse::noContent(),
        ]);

        $client->clearIdempotencyKeyGenerator();

        /** @var Response $response */
        $response = $client->send(new DynamicDeleteRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
    }

    #[DataProvider('providesMutatingRequests')]
    #[Test]
    public function idempotency_key_is_used_on_mutating_requests($request, $response)
    {
        $client = new MockMollieClient([
            get_class($request) => $response,
        ]);

        $client->setIdempotencyKey('idempotentFooBar');

        $response = $client->send($request);

        $this->assertTrue($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
        $this->assertEquals('idempotentFooBar', $response->getPendingRequest()->headers()->get(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
    }

    public static function providesMutatingRequests(): array
    {
        return [
            'delete' => [
                new DynamicDeleteRequest(''),
                MockResponse::noContent(),
            ],
            'post' => [
                new CreatePaymentRequest(
                    'test',
                    new Money('EUR', '100.00'),
                ),
                MockResponse::ok('payment'),
            ],
            'patch' => [
                new UpdatePaymentRequest(
                    'tr_payment-id',
                    'test',
                ),
                MockResponse::ok('payment'),
            ],
        ];
    }

    #[Test]
    public function idempotency_key_is_not_used_on_get_requests()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::noContent(),
        ]);

        $client->setIdempotencyKey('idempotentFooBar');

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
        $this->assertNull($client->getIdempotencyKey());
    }

    #[Test]
    public function idempotency_key_resets_after_each_request()
    {
        $client = new MockMollieClient([
            DynamicDeleteRequest::class => MockResponse::noContent(),
        ]);

        $client->setIdempotencyKey('idempotentFooBar');

        $this->assertEquals('idempotentFooBar', $client->getIdempotencyKey());

        $client->send(new DynamicDeleteRequest(''));

        $this->assertNull($client->getIdempotencyKey());
    }

    #[Test]
    public function explicit_idempotency_key_is_cleared_after_failure_and_not_inherited()
    {
        $client = new MockMollieClient([
            DynamicPostRequest::class => MockResponse::unprocessableEntity('Invalid request'),
            DynamicDeleteRequest::class => MockResponse::noContent(),
        ]);

        $client->clearIdempotencyKeyGenerator();
        $client->setIdempotencyKey('idempotentFooBar');

        try {
            $client->send(new DynamicPostRequest(''));
            $this->fail('Expected the request to fail.');
        } catch (ValidationException) {
            $this->assertNull($client->getIdempotencyKey());
        }

        $response = $client->send(new DynamicDeleteRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
    }

    #[Test]
    public function it_uses_the_idempotency_key_generator()
    {
        $client = new MockMollieClient([
            DynamicDeleteRequest::class => MockResponse::noContent(),
        ]);

        $fakeIdempotencyKeyGenerator = new FakeIdempotencyKeyGenerator;
        $fakeIdempotencyKeyGenerator->setFakeKey('fake-idempotency-key');

        $client->setIdempotencyKeyGenerator($fakeIdempotencyKeyGenerator);

        $this->assertNull($client->getIdempotencyKey());

        $response = $client->send(new DynamicDeleteRequest(''));

        $this->assertEquals('fake-idempotency-key', $response->getPendingRequest()->headers()->get(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
        $this->assertNull($client->getIdempotencyKey());
    }

    #[Test]
    public function testmode_is_added_to_request_when_enabled()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertTrue($response->getPendingRequest()->query()->has('testmode'));
        $this->assertEquals('true', $response->getPendingRequest()->query()->get('testmode'));
    }

    #[Test]
    public function testmode_is_removed_when_using_api_key_authentication()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        $client->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->query()->has('testmode'));
    }

    #[Test]
    public function testmode_is_not_removed_when_not_using_api_key_authentication()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        // Not setting an API key, so using default authentication
        // by default we use access token authentication in tests
        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertTrue($response->getPendingRequest()->query()->has('testmode'));
        $this->assertEquals('true', $response->getPendingRequest()->query()->get('testmode'));
    }

    #[Test]
    public function when_debugging_is_enabled_the_request_is_sanitized_when_an_exception_is_thrown_to_prevent_leaking_sensitive_data()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => new MockResponse([
                'status' => 503,
                'title' => 'Service Unavailable',
                'detail' => 'The service is temporarily unavailable.',
            ], 503),
        ]);

        $client->test(true);

        try {
            $client
                ->debug()
                ->send(new DynamicGetRequest(''));
        } catch (RequestException $e) {
            $this->assertArrayNotHasKey('Authorization', $e->getRequest()->getHeaders());
            $this->assertArrayNotHasKey('User-Agent', $e->getRequest()->getHeaders());
            $this->assertArrayNotHasKey('X-Mollie-Client-Info', $e->getRequest()->getHeaders());
        }
    }

    #[Test]
    public function debugging_request_captures_request_information()
    {
        $requestCaptured = false;
        $capturedRequest = null;

        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        $client->debugRequest(function ($pendingRequest, $psrRequest) use (&$requestCaptured, &$capturedRequest) {
            $requestCaptured = true;
            $capturedRequest = $pendingRequest;
        });

        $client->send(new DynamicGetRequest(''));

        $this->assertTrue($requestCaptured, 'Request debug callback was not called');
        $this->assertNotNull($capturedRequest);
        $this->assertInstanceOf(PendingRequest::class, $capturedRequest);
    }

    #[Test]
    public function debugging_response_captures_response_information()
    {
        $responseCaptured = false;
        $capturedResponse = null;

        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        $client->debugResponse(function ($response, $psrResponse) use (&$responseCaptured, &$capturedResponse) {
            $responseCaptured = true;
            $capturedResponse = $response;
        });

        $client->send(new DynamicGetRequest(''));

        $this->assertTrue($responseCaptured, 'Response debug callback was not called');
        $this->assertNotNull($capturedResponse);
        $this->assertInstanceOf(Response::class, $capturedResponse);
    }

    #[Test]
    public function debugging_with_die_flag_exits_after_debug()
    {
        $dieWasCalled = false;
        $originalDieHandler = Debugger::$dieHandler;

        try {
            $client = new MockMollieClient([
                DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
            ]);

            Debugger::$dieHandler = function () use (&$dieWasCalled) {
                $dieWasCalled = true;
            };

            $client->debug(true);
            $client->send(new DynamicGetRequest(''));

            $this->assertTrue($dieWasCalled, 'Die handler was not called');
        } finally {
            Debugger::$dieHandler = $originalDieHandler;
        }
    }

    #[Test]
    public function debugging_removes_sensitive_data_from_request()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => new MockResponse([
                'status' => 503,
                'title' => 'Service Unavailable',
                'detail' => 'The service is temporarily unavailable.',
            ], 503),
        ]);

        try {
            $client
                ->debug()
                ->send(new DynamicGetRequest(''));
        } catch (RequestException $e) {
            $this->assertArrayNotHasKey('Authorization', $e->getRequest()->getHeaders());
            $this->assertArrayNotHasKey('User-Agent', $e->getRequest()->getHeaders());
            $this->assertArrayNotHasKey('X-Mollie-Client-Info', $e->getRequest()->getHeaders());
        }
    }

    #[Test]
    public function can_hydrate_response_into_custom_resource_wrapper_class()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('{"resource": "payment"}'),
        ]);

        /** @var DummyResourceWrapper $response */
        $response = $client->send(
            (new DynamicGetRequest(''))
                ->setHydratableResource(new WrapperResource(DummyResourceWrapper::class))
        );

        $this->assertInstanceOf(DummyResourceWrapper::class, $response);
    }

    #[Test]
    public function empty_or_null_query_parameters_are_not_added_to_the_request()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => function (PendingRequest $pendingRequest) {
                $this->assertEquals([
                    'filled' => 'bar',
                ], $pendingRequest->query()->all());
                $this->assertEquals('filled=bar', $pendingRequest->getUri()->getQuery());

                return MockResponse::noContent();
            },
        ]);

        $client->send(new DynamicGetRequest('', [
            'filled' => 'bar',
            'empty' => '',
            'null' => null,
            'empty_array' => [],
        ]));
    }

    #[Test]
    public function empty_or_null_payload_parameters_are_not_added_to_the_request()
    {
        $client = new MockMollieClient([
            DummyPostRequest::class => function (PendingRequest $pendingRequest) {
                $this->assertEquals([
                    'filled' => 'bar',
                ], $pendingRequest->payload()->all());

                return MockResponse::noContent();
            },
        ]);

        $request = new DummyPostRequest;

        $request->payload()->set([
            'filled' => 'bar',
            'empty' => '',
            'null' => null,
            'empty_array' => [],
        ]);

        $client->send($request);
    }

    #[Test]
    public function a_response_with_empty_body_is_not_hydrated()
    {
        $client = new MockMollieClient([
            DynamicPostRequest::class => MockResponse::noContent(),
        ]);

        $request = new DynamicPostRequest('dummy');

        $request->setHydratableResource(new WrapperResource(DummyResourceWrapper::class));

        /** @var Response $response */
        $response = $client->send($request);

        $this->assertInstanceOf(Response::class, $response);
    }
}

class DummyResourceWrapper extends ResourceWrapper
{
    public static function fromResource($resource): self
    {
        return (new self)->wrap($resource);
    }
}

class DummyPostRequest extends Request implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    public function resolveResourcePath(): string
    {
        return 'dummy';
    }
}
