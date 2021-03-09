<?php

namespace Tests\Mollie\API\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use Mollie\Api\HttpAdapter\Guzzle6And7MollieHttpAdapter;
use Mollie\Api\HttpAdapter\HttpAdapterPicker;
use PHPUnit\Framework\TestCase;

class HttpAdapterPickerTest extends TestCase
{
    /** @test */
    public function createsAGuzzleAdapterIfNullIsPassedAndGuzzleIsDetected()
    {
        $picker = new HttpAdapterPicker;

        $adapter = $picker->pickHttpAdapter(null);

        $this->assertInstanceOf(Guzzle6And7MollieHttpAdapter::class, $adapter);
    }

    /** @test */
    public function returnsTheAdapterThatWasPassedIn()
    {
        $picker = new HttpAdapterPicker;
        $mockAdapter = new MockHttpAdapter;

        $adapter = $picker->pickHttpAdapter($mockAdapter);

        $this->assertInstanceOf(MockHttpAdapter::class, $adapter);
        $this->assertEquals($mockAdapter, $adapter);
    }

    /** @test */
    public function wrapsAGuzzleClientIntoAnAdapter()
    {
        $picker = new HttpAdapterPicker;
        $guzzleClient = new GuzzleClient;

        $adapter = $picker->pickHttpAdapter($guzzleClient);

        $this->assertInstanceOf(Guzzle6And7MollieHttpAdapter::class, $adapter);
    }
}
