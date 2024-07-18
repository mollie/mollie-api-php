<?php

namespace Tests\Mollie\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException;
use Mollie\Api\Http\Adapter\CurlMollieHttpAdapter;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Idempotency\FakeIdempotencyKeyGenerator;
use Mollie\Api\MollieApiClient;
use Tests\Mollie\TestHelpers\FakeHttpAdapter;

class MollieApiClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $guzzleClient;

    /**
     * @var MollieApiClient
     */
    private MollieApiClient $mollieApiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClient = $this->createMock(Client::class);
        $this->mollieApiClient = new MollieApiClient($this->guzzleClient);

        $this->mollieApiClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
    }

    public function testPerformHttpCallReturnsBodyAsObject()
    {
        $response = new Response(200, [], '{"resource": "payment"}');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);


        $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');

        $this->assertEquals(
            (object)['resource' => 'payment'],
            $parsedResponse->decode()
        );
    }

    public function testPerformHttpCallCreatesApiExceptionCorrectly()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $response = new Response(422, [], '{
            "status": 422,
            "title": "Unprocessable Entity",
            "detail": "Non-existent parameter \"recurringType\" for this API call. Did you mean: \"sequenceType\"?",
            "field": "recurringType",
            "_links": {
                "documentation": {
                    "href": "https://docs.mollie.com/guides/handling-errors",
                    "type": "text/html"
                }
            }
        }');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        try {
            $this->mollieApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertEquals('recurringType', $e->getField());
            $this->assertEquals('https://docs.mollie.com/guides/handling-errors', $e->getDocumentationUrl());
            $this->assertEquals($response, $e->getResponse());

            throw $e;
        }
    }

    public function testPerformHttpCallCreatesApiExceptionWithoutFieldAndDocumentationUrl()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $response = new Response(422, [], '{
            "status": 422,
            "title": "Unprocessable Entity",
            "detail": "Non-existent parameter \"recurringType\" for this API call. Did you mean: \"sequenceType\"?"
        }');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        try {
            $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertNull($e->getField());
            $this->assertNull($e->getDocumentationUrl());
            $this->assertEquals($response, $e->getResponse());

            throw $e;
        }
    }

    public function testCanBeSerializedAndUnserialized()
    {
        $client = new FakeMollieApiClient($this->createMock(Client::class));

        $client->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
        $client->setApiEndpoint("https://mymollieproxy.local");
        $serialized = \serialize($client);

        $this->assertStringNotContainsString('test_foobarfoobarfoobarfoobarfoobar', $serialized, "API key should not be in serialized data or it will end up in caches.");

        /** @var FakeMollieApiClient $client_copy */
        $client_copy = unserialize($serialized);

        $this->assertEmpty($client_copy->getApiKey(), "API key should not have been remembered");
        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $client_copy->getHttpClient(), "A Guzzle client should have been set.");
        $this->assertNull($client_copy->usesOAuth());
        $this->assertEquals("https://mymollieproxy.local", $client_copy->getApiEndpoint(), "The API endpoint should be remembered");

        $this->assertNotEmpty($client_copy->customerPayments);
        $this->assertNotEmpty($client_copy->payments);
        $this->assertNotEmpty($client_copy->methods);
        // no need to assert them all.
    }

    public function testResponseBodyCanBeReadMultipleTimesIfMiddlewareReadsItFirst()
    {
        $response = new Response(200, [], '{"resource": "payment"}');

        // Before the MollieApiClient gets the response, some middleware reads the body first.
        $bodyAsReadFromMiddleware = (string) $response->getBody();

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');

        $this->assertEquals(
            '{"resource": "payment"}',
            $bodyAsReadFromMiddleware
        );

        $this->assertEquals(
            (object)['resource' => 'payment'],
            $parsedResponse->decode()
        );
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
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $mollieClient->performHttpCallToFullUrl('GET', '', '');

        $usedHeaders = $fakeAdapter->getUsedHeaders();

        # these change through environments
        # just make sure its existing
        $this->assertArrayHasKey('User-Agent', $usedHeaders);
        $this->assertArrayHasKey('X-Mollie-Client-Info', $usedHeaders);

        # these should be exactly the expected values
        $this->assertEquals('Bearer test_foobarfoobarfoobarfoobarfoobar', $usedHeaders['Authorization']);
        $this->assertEquals('application/json', $usedHeaders['Accept']);
        $this->assertEquals('application/json', $usedHeaders['Content-Type']);
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
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $mollieClient = new MollieApiClient($fakeAdapter);
        $mollieClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');

        $mollieClient->performHttpCallToFullUrl('GET', '');

        $this->assertEquals(false, isset($fakeAdapter->getUsedHeaders()['Content-Type']));
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
     * @param $httpMethod
     * @return void
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

class FakeMollieApiClient extends MollieApiClient
{
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getHttpClient(): MollieHttpAdapterContract
    {
        return $this->httpClient;
    }
}
