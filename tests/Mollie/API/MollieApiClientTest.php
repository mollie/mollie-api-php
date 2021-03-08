<?php
namespace Tests\Mollie\Api;

use Eloquent\Liberator\Liberator;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\HttpAdapter\Guzzle6And7MollieHttpAdapter;
use Mollie\Api\MollieApiClient;

class MollieApiClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzleClient;

    /**
     * @var MollieApiClient
     */
    private $mollieApiClient;

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
            $parsedResponse
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
            $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');
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
        $this->mollieApiClient->setApiEndpoint("https://mymollieproxy.local");
        $serialized = \serialize($this->mollieApiClient);

        $this->assertStringNotContainsString('test_foobarfoobarfoobarfoobarfoobar', $serialized, "API key should not be in serialized data or it will end up in caches.");

        /** @var MollieApiClient $client_copy */
        $client_copy = Liberator::liberate(unserialize($serialized));

        $this->assertEmpty($client_copy->apiKey, "API key should not have been remembered");
        $this->assertInstanceOf(Guzzle6And7MollieHttpAdapter::class, $client_copy->httpClient, "A Guzzle client should have been set.");
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
            $parsedResponse
        );
    }
}
