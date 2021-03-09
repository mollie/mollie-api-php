<?php

namespace Tests\Mollie\API\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use Mollie\Api\HttpAdapter\Guzzle6And7MollieHttpAdapter;
use Mollie\Api\HttpAdapter\MollieHttpAdapterPicker;
use PHPUnit\Framework\TestCase;

class MollieHttpAdapterPickerTest extends TestCase
{
    /** @test */
    public function createsAGuzzleAdapterIfNullIsPassedAndGuzzleIsDetected()
    {
        $picker = new MollieHttpAdapterPicker;

        $adapter = $picker->pickHttpAdapter(null);

        $this->assertInstanceOf(Guzzle6And7MollieHttpAdapter::class, $adapter);
    }

    /** @test */
    public function returnsTheAdapterThatWasPassedIn()
    {
        $picker = new MollieHttpAdapterPicker;
        $mockAdapter = new MockMollieHttpAdapter;

        $adapter = $picker->pickHttpAdapter($mockAdapter);

        $this->assertInstanceOf(MockMollieHttpAdapter::class, $adapter);
        $this->assertEquals($mockAdapter, $adapter);
    }

    /** @test */
    public function wrapsAGuzzleClientIntoAnAdapter()
    {
        $picker = new MollieHttpAdapterPicker;
        $guzzleClient = new GuzzleClient;

        $adapter = $picker->pickHttpAdapter($guzzleClient);

        $this->assertInstanceOf(Guzzle6And7MollieHttpAdapter::class, $adapter);
    }
}
