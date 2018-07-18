<?php
namespace Tests\Mollie\Api;

use Eloquent\Liberator\Liberator;
use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\Mock\Client;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

class MollieApiClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var MollieApiClient
     */
    private $mollieApiClient;

    protected function setUp()
    {
        parent::setUp();

        $this->httpClient    = new Client();

        $this->mollieApiClient = new MollieApiClient($this->httpClient);

        $this->mollieApiClient->setApiKey('test_foobarfoobarfoobarfoobarfoobar');
    }

    public function testPerformHttpCallReturnsBodyAsObject()
    {
        $response = new Response(200, [], '{"resource": "payment"}');

        $this->httpClient->addResponse($response);

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

        $this->httpClient->addResponse($response);

        try {
            $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertEquals('recurringType', $e->getField());
            $this->assertEquals('https://docs.mollie.com/guides/handling-errors', $e->getDocumentationUrl());

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

        $this->httpClient->addResponse($response);

        try {
            $parsedResponse = $this->mollieApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertNull($e->getField());
            $this->assertNull($e->getDocumentationUrl());

            throw $e;
        }
    }

    public function testCanBeSerializedAndUnserialized()
    {
        $this->mollieApiClient->setApiEndpoint("https://mymollieproxy.local");
        $serialized = \serialize($this->mollieApiClient);

        $this->assertNotContains('test_foobarfoobarfoobarfoobarfoobar', $serialized, "API key should not be in serialized data or it will end up in caches.");

        /** @var MollieApiClient $client_copy */
        $client_copy = Liberator::liberate(unserialize($serialized));

        $this->assertEmpty($client_copy->apiKey, "API key should not have been remembered");
        $this->assertInstanceOf(HttpClient::class, $client_copy->httpClient, "A Guzzle client should have been set.");
        $this->assertNull($client_copy->usesOAuth());
        $this->assertEquals("https://mymollieproxy.local", $client_copy->getApiEndpoint(), "The API endpoint should be remembered");

        $this->assertNotEmpty($client_copy->customerPayments);
        $this->assertNotEmpty($client_copy->payments);
        $this->assertNotEmpty($client_copy->methods);
        // no need to assert them all.
    }
}
