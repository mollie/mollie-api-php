<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException;
use Mollie\Api\Http\Adapter\CurlMollieHttpAdapter;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Payload\CreatePayment;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response as HttpResponse;
use Mollie\Api\Idempotency\FakeIdempotencyKeyGenerator;
use Mollie\Api\MollieApiClient;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Mollie\TestHelpers\FakeHttpAdapter;

class MollieApiClientTest extends TestCase
{
    public function testSendReturnsBodyAsObject()
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

    public function testSendCreatesApiExceptionCorrectly()
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
            $this->assertEquals('https://docs.mollie.com/guides/handling-errors', $e->getDocumentationUrl());

            $mockResponse->assertResponseBodyEquals($e->getResponse());

            throw $e;
        }
    }

    public function testSendCreatesApiExceptionWithoutFieldAndDocumentationUrl()
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

    public function testCanBeSerializedAndUnserialized()
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

    public function testEnablingDebuggingThrowsAnExceptionIfHttpAdapterDoesNotSupportIt()
    {
        $this->expectException(HttpAdapterDoesNotSupportDebuggingException::class);
        $client = new MollieApiClient(new CurlMollieHttpAdapter);

        $client->enableDebugging();
    }

    public function testDisablingDebuggingThrowsAnExceptionIfHttpAdapterDoesNotSupportIt()
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
    public function testCorrectRequestHeaders()
    {
        $client = new MockClient([
            CreatePaymentRequest::class => new MockResponse(200, '{"resource": "payment"}'),
        ]);

        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $response = $client->send(new CreatePaymentRequest(new CreatePayment(
            'test',
            new Money('EUR', '100.00'),
        )));

        $usedHeaders = $response->getResponse()->getPendingRequest()->headers()->all();

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
    public function testNoContentTypeWithoutProvidedBody()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(204, ''),
        ]);

        /** @var HttpResponse $response */
        $response = $client->send(new DynamicGetRequest(''));

        $this->assertEquals(false, $response->getPendingRequest()->headers()->get('Content-Type'));
    }

    public function testIfNoIdempotencyKeyIsSetNoReferenceIsIncludedInTheRequestHeaders()
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        // ... Not setting an idempotency key here

        $mollieClient->performHttpCallToFullUrl('GET', '');

        $this->assertFalse(isset($fakeAdapter->getUsedHeaders()['Idempotency-Key']));
    }

    public function testIdempotencyKeyIsUsedOnMutatingRequests()
    {
        $this->assertIdempotencyKeyIsUsedForMethod('POST');
        $this->assertIdempotencyKeyIsUsedForMethod('PATCH');
        $this->assertIdempotencyKeyIsUsedForMethod('DELETE');
    }

    public function testIdempotencyKeyIsNotUsedOnGetRequests()
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $mollieClient->setIdempotencyKey('idempotentFooBar');

        $mollieClient->performHttpCallToFullUrl('GET', '');

        $this->assertFalse(isset($fakeAdapter->getUsedHeaders()['Idempotency-Key']));
    }

    public function testIdempotencyKeyResetsAfterEachRequest()
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $mollieClient->setIdempotencyKey('idempotentFooBar');
        $this->assertEquals('idempotentFooBar', $mollieClient->getIdempotencyKey());

        $mollieClient->performHttpCallToFullUrl('POST', '');

        $this->assertNull($mollieClient->getIdempotencyKey());
    }

    public function testItUsesTheIdempotencyKeyGenerator()
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);
        $fakeIdempotencyKeyGenerator = new FakeIdempotencyKeyGenerator;
        $fakeIdempotencyKeyGenerator->setFakeKey('fake-idempotency-key');

        $mollieClient = new MollieApiClient($fakeAdapter, null, $fakeIdempotencyKeyGenerator);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $this->assertNull($mollieClient->getIdempotencyKey());

        $mollieClient->performHttpCallToFullUrl('POST', '');

        $this->assertEquals('fake-idempotency-key', $fakeAdapter->getUsedHeaders()['Idempotency-Key']);
        $this->assertNull($mollieClient->getIdempotencyKey());
    }

    /**
     * @return void
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatform
     * @throws \Mollie\Api\Exceptions\UnrecognizedClientException
     */
    private function assertIdempotencyKeyIsUsedForMethod($httpMethod)
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $mollieClient->setIdempotencyKey('idempotentFooBar');

        $mollieClient->performHttpCallToFullUrl($httpMethod, '');

        $this->assertTrue(isset($fakeAdapter->getUsedHeaders()['Idempotency-Key']));
        $this->assertEquals('idempotentFooBar', $fakeAdapter->getUsedHeaders()['Idempotency-Key']);
    }
}
