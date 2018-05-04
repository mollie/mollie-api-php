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

    protected function mockApiCall(Request $expectedRequest, Response $response)
    {
        $this->guzzleClient = $this->createMock(Client::class);

        $this->apiClient = new MollieApiClient($this->guzzleClient);
        $this->apiClient->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willReturnCallback(function (Request $request) use ($expectedRequest, $response) {
                $this->assertEquals($expectedRequest->getMethod(), $request->getMethod());

                $this->assertEquals(
                    $expectedRequest->getUri()->getPath(),
                    $request->getUri()->getPath()
                );

                $this->assertEquals(
                    $expectedRequest->getUri()->getQuery(),
                    $request->getUri()->getQuery()
                );

                $requestBody = $request->getBody()->getContents();
                $expectedBody = $expectedRequest->getBody()->getContents();

                if (strlen($expectedBody) > 0 && strlen($requestBody) > 0) {
                    $this->assertJsonStringEqualsJsonString(
                        $expectedBody,
                        $requestBody
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
