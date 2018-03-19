<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\MollieApiClient;

abstract class BaseEndpointTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzle_client;

    /**
     * @var MollieApiClient
     */
    protected $api_client;

    protected function mockApiCall(Request $expected_request, Response $response)
    {
        $this->guzzle_client = $this->createMock(Client::class);

        $this->api_client = new MollieApiClient($this->guzzle_client);
        $this->api_client->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

        $this->guzzle_client
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willReturnCallback(function (Request $request) use ($expected_request, $response) {
                $this->assertEquals($expected_request->getMethod(), $request->getMethod());

                $this->assertEquals(
                    $expected_request->getUri()->getPath(),
                    $request->getUri()->getPath()
                );

                $request_body = $request->getBody()->getContents();
                $expected_body = $expected_request->getBody()->getContents();

                if(strlen($expected_body) > 0 && strlen($request_body) > 0){
                    $this->assertJsonStringEqualsJsonString(
                        $expected_body,
                        $request_body
                    );
                }

                return $response;
            });
    }

}