<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use Mollie\Api\MollieApiClient;

abstract class BaseEndpointTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var
     */
    protected $messageFactory;

    /**
     * @var MollieApiClient
     */
    protected $apiClient;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function mockApiCall(Response $response)
    {
        $this->httpClient = new Client();
        $this->httpClient->addResponse($response);

        $this->apiClient = new MollieApiClient($this->httpClient);
        $this->apiClient->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
    }

    protected function assertRequest(Request $expected_request)
    {
        $this->assertEquals($expected_request->getMethod(), $this->httpClient->getLastRequest()->getMethod());
        $this->assertEquals($expected_request->getUri(), $this->httpClient->getLastRequest()->getUri());
    }

    protected function copy($array, $object)
    {
        foreach ($array as $property => $value) {
            $object->$property = $value;
        }

        return $object;
    }
}
