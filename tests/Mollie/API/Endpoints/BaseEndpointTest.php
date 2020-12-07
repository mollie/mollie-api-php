<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\MollieApiClient;

abstract class BaseEndpointTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleClient;

    /**
     * @var MollieApiClient
     */
    protected $apiClient;

    protected function mockApiCall(Request $expectedRequest, Response $response, $oAuthClient = false)
    {
        $this->guzzleClient = $this->createMock(Client::class);

        $this->apiClient = new MollieApiClient($this->guzzleClient);

        if (! $oAuthClient) {
            $this->apiClient->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
        } else {
            $this->apiClient->setAccessToken("access_Wwvu7egPcJLLJ9Kb7J632x8wJ2zMeJ");
        }

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willReturnCallback(function (Request $request) use ($expectedRequest, $response) {
                $this->assertEquals($expectedRequest->getMethod(), $request->getMethod(), "HTTP method must be identical");

                $this->assertEquals(
                    $expectedRequest->getUri()->getPath(),
                    $request->getUri()->getPath(),
                    "URI path must be identical"
                );

                $this->assertEquals(
                    $expectedRequest->getUri()->getQuery(),
                    $request->getUri()->getQuery(),
                    'Query string parameters must be identical'
                );

                $requestBody = $request->getBody()->getContents();
                $expectedBody = $expectedRequest->getBody()->getContents();

                if (strlen($expectedBody) > 0 && strlen($requestBody) > 0) {
                    $this->assertJsonStringEqualsJsonString(
                        $expectedBody,
                        $requestBody,
                        "HTTP body must be identical"
                    );
                }

                return $response;
            });
    }

    protected function copy($array, $object)
    {
        foreach ($array as $property => $value) {
            $object->$property = $value;
        }

        return $object;
    }
}
