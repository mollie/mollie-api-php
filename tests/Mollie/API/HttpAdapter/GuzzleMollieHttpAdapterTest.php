<?php

namespace Tests\Mollie\API\HttpAdapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class GuzzleMollieHttpAdapterTest extends TestCase
{
    /** @test */
    public function testDebuggingIsSupported()
    {
        $adapter = GuzzleMollieHttpAdapter::createDefault();
        $this->assertTrue($adapter instanceof SupportsDebuggingContract);
        $this->assertFalse($adapter->debuggingIsActive());

        $adapter->enableDebugging();
        $this->assertTrue($adapter->debuggingIsActive());

        $adapter->disableDebugging();
        $this->assertFalse($adapter->debuggingIsActive());
    }

    /** @test */
    public function whenDebuggingAnApiExceptionIncludesTheRequest()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new GuzzleMollieHttpAdapter($guzzleClient);
        $adapter->enableDebugging();

        try {
            $adapter->send(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
                [],
                /** @lang JSON */
                '{ "foo": "bar" }'
            );
        } catch (ApiException $e) {
            $this->assertInstanceOf(RequestInterface::class, $e->getRequest());
        }
    }

    /** @test */
    public function whenNotDebuggingAnApiExceptionIsExcludedFromTheRequest()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willThrowException(
                new ConnectException(
                    'Mock exception',
                    new Request('POST', 'https://api.mollie.com')
                )
            );

        $adapter = new GuzzleMollieHttpAdapter($guzzleClient);
        $this->assertFalse($adapter->debuggingIsActive());

        try {
            $adapter->send(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
                [],
                /** @lang JSON */
                '{ "foo": "bar" }'
            );
        } catch (ApiException $e) {
            $this->assertNull($e->getRequest());
        }
    }
}
