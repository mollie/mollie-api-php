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

    protected function setUp()
    {
        parent::setUp();

        $this->guzzle_client = $this->createMock(Client::class);
        $this->api_client = new MollieApiClient($this->guzzle_client);
        $this->api_client->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
    }

    protected function mockApiCall(Request $expected_request, Response $response)
    {
        $this->guzzle_client
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willReturnCallback(function (Request $request) use ($expected_request, $response) {
                $this->assertEquals($expected_request->getMethod(), $request->getMethod());

                $this->assertEquals(
                    $expected_request->getUri()->getPath(),
                    $request->getUri()->getPath()
                );

                $this->assertJsonStringEqualsJsonString(
                    $expected_request->getBody()->getContents(),
                    $request->getBody()->getContents()
                );

                return $response;
            });
    }

}