<?php

namespace Tests;

use GuzzleHttp\Client;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException;
use Mollie\Api\Http\Adapter\CurlMollieHttpAdapter;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\UpdatePaymentPayload;
use Mollie\Api\Http\Middleware\ApplyIdempotencyKey;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Http\Response as HttpResponse;
use Mollie\Api\Idempotency\FakeIdempotencyKeyGenerator;
use Mollie\Api\MollieApiClient;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\Requests\DynamicDeleteRequest;
use Tests\Fixtures\Requests\DynamicGetRequest;

class MollieApiClientTest extends TestCase
{
    /** @test */
    public function send_returns_body_as_object()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertEquals(
            (object) ['resource' => 'payment'],
            $response->json()
        );
    }

    /** @test */
    public function send_creates_api_exception_correctly()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $client = new MockClient([
            DynamicGetRequest::class => $mockResponse = new MockResponse(422, 'unprocessable-entity-with-field'),
        ]);

        try {
            $client->send(new DynamicGetRequest(''));
        } catch (ApiException $e) {
            $this->assertEquals('recurringType', $e->getField());
            $this->assertNotEmpty($e->getDocumentationUrl());

            $mockResponse->assertResponseBodyEquals($e->getResponse());

            throw $e;
        }
    }

    /** @test */
    public function send_creates_api_exception_without_field_and_documentation_url()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $client = new MockClient([
            DynamicGetRequest::class => $mockResponse = new MockResponse(422, 'unprocessable-entity'),
        ]);

        try {
            $client->send(new DynamicGetRequest(''));
        } catch (ApiException $e) {
            $this->assertNull($e->getField());
            $this->assertNull($e->getDocumentationUrl());
            $mockResponse->assertResponseBodyEquals($e->getResponse());

            throw $e;
        }
    }

    /** @test */
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
        $this->assertNotEmpty($client_copy->methods);
        // no need to assert them all.
    }

    /** @test */
    public function enabling_debugging_throws_an_exception_if_http_adapter_does_not_support_it()
    {
        $this->expectException(HttpAdapterDoesNotSupportDebuggingException::class);
        $client = new MollieApiClient(new CurlMollieHttpAdapter);

        $client->enableDebugging();
    }

    /** @test */
    public function disabling_debugging_throws_an_exception_if_http_adapter_does_not_support_it()
    {
        $this->expectException(HttpAdapterDoesNotSupportDebuggingException::class);
        $client = new MollieApiClient(new CurlMollieHttpAdapter);

        $client->disableDebugging();
    }

    /**
     * This test verifies that our request headers are correctly sent to Mollie.
     * If these are broken, it could be that some payments do not work.
     *
     * @throws ApiException
     */
    /** @test */
    public function correct_request_headers()
    {
        $client = new MockClient([
            CreatePaymentRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $response = $client->send(new CreatePaymentRequest(new CreatePaymentPayload(
            'test',
            new Money('EUR', '100.00'),
        )));

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
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatform
     * @throws \Mollie\Api\Exceptions\UnrecognizedClientException
     */
    /** @test */
    public function no_content_type_without_provided_body()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(204, ''),
        ]);

        /** @var HttpResponse $response */
        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has('Content-Type'));
    }

    /** @test */
    public function no_idempotency_is_set_if_no_key_nor_generator_are_set()
    {
        $client = new MockClient([
            DynamicDeleteRequest::class => new MockResponse(204, ''),
        ]);

        $client->clearIdempotencyKeyGenerator();

        /** @var HttpResponse $response */
        $response = $client->send(new DynamicDeleteRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
    }

    /**
     * @dataProvider providesMutatingRequests
     *
     * @test
     */
    public function idempotency_key_is_used_on_mutating_requests($request, $response)
    {
        $client = new MockClient([
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
                new MockResponse(204, ''),
            ],
            'post' => [
                new CreatePaymentRequest(new CreatePaymentPayload(
                    'test',
                    new Money('EUR', '100.00'),
                )),
                new MockResponse(200, 'payment'),
            ],
            'patch' => [
                new UpdatePaymentRequest('tr_payment-id', new UpdatePaymentPayload(
                    'test',
                )),
                new MockResponse(200, 'payment'),
            ],
        ];
    }

    /** @test */
    public function idempotency_key_is_not_used_on_get_requests()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(204),
        ]);

        $client->setIdempotencyKey('idempotentFooBar');

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->headers()->has(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
    }

    /** @test */
    public function idempotency_key_resets_after_each_request()
    {
        $client = new MockClient([
            DynamicDeleteRequest::class => new MockResponse(204),
        ]);

        $client->setIdempotencyKey('idempotentFooBar');

        $this->assertEquals('idempotentFooBar', $client->getIdempotencyKey());

        $client->send(new DynamicDeleteRequest(''));

        $this->assertNull($client->getIdempotencyKey());
    }

    /** @test */
    public function it_uses_the_idempotency_key_generator()
    {
        $client = new MockClient([
            DynamicDeleteRequest::class => new MockResponse(204),
        ]);

        $fakeIdempotencyKeyGenerator = new FakeIdempotencyKeyGenerator;
        $fakeIdempotencyKeyGenerator->setFakeKey('fake-idempotency-key');

        $client->setIdempotencyKeyGenerator($fakeIdempotencyKeyGenerator);

        $this->assertNull($client->getIdempotencyKey());

        $response = $client->send(new DynamicDeleteRequest(''));

        $this->assertEquals('fake-idempotency-key', $response->getPendingRequest()->headers()->get(ApplyIdempotencyKey::IDEMPOTENCY_KEY_HEADER));
        $this->assertNull($client->getIdempotencyKey());
    }

    /** @test */
    public function testmode_is_added_to_request_when_enabled()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertTrue($response->getPendingRequest()->query()->has('testmode'));
        $this->assertTrue($response->getPendingRequest()->query()->get('testmode'));
    }

    /** @test */
    public function testmode_is_removed_when_using_api_key_authentication()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        $client->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertFalse($response->getPendingRequest()->query()->has('testmode'));
    }

    /** @test */
    public function testmode_is_not_removed_when_not_using_api_key_authentication()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        // Not setting an API key, so using default authentication
        // by default we use access token authentication in tests
        $client->test(true);

        $response = $client->send(new DynamicGetRequest(''));

        $this->assertTrue($response->getPendingRequest()->query()->has('testmode'));
        $this->assertTrue($response->getPendingRequest()->query()->get('testmode'));
    }
}
