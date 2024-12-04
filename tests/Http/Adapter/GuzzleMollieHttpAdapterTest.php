<?php

namespace Tests\Http\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Psr\Http\Message\RequestInterface;
use Tests\Fixtures\MockClient;
use Tests\TestCase;

class GuzzleMollieHttpAdapterTest extends TestCase
{
    /** @test */
    public function test_debugging_is_supported()
    {
        $adapter = GuzzleMollieHttpAdapter::createDefault();
        $this->assertFalse($adapter->debuggingIsActive());

        $adapter->enableDebugging();
        $this->assertTrue($adapter->debuggingIsActive());

        $adapter->disableDebugging();
        $this->assertFalse($adapter->debuggingIsActive());
    }

    /** @test */
    public function when_debugging_an_api_exception_includes_the_request()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new GuzzleMollieHttpAdapter($guzzleClient);
        $adapter->enableDebugging();

        $request = new DynamicGetRequest('https://api.mollie.com/v2/payments');
        $pendingRequest = new PendingRequest(new MockClient, $request);

        try {
            $adapter->sendRequest($pendingRequest);
        } catch (ApiException $e) {
            $this->assertInstanceOf(RequestInterface::class, $e->getRequest());
        }
    }

    /** @test */
    public function when_not_debugging_an_api_exception_is_excluded_from_the_request()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new GuzzleMollieHttpAdapter($guzzleClient);
        $this->assertFalse($adapter->debuggingIsActive());

        $request = new DynamicGetRequest('https://api.mollie.com/v2/payments');
        $pendingRequest = new PendingRequest(new MockClient, $request);

        try {
            $adapter->sendRequest($pendingRequest);
        } catch (ApiException $e) {
            $this->assertNull($e->getRequest());
        }
    }
}
