<?php

namespace Tests\Mollie\API\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use Mollie\Api\Exceptions\UnrecognizedClientException;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Adapter\MollieHttpAdapterPicker;
use PHPUnit\Framework\TestCase;

class MollieHttpAdapterPickerTest extends TestCase
{
    /** @test */
    public function createsAGuzzleAdapterIfNullIsPassedAndGuzzleIsDetected()
    {
        $picker = new MollieHttpAdapterPicker;

        $adapter = $picker->pickHttpAdapter(null);

        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $adapter);
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

        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $adapter);
    }

    /** @test */
    public function throwsAnExceptionWhenReceivingAnUnrecognizedClient()
    {
        $this->expectExceptionObject(new UnrecognizedClientException('The provided http client or adapter was not recognized'));
        $picker = new MollieHttpAdapterPicker;
        $unsupportedClient = (object) ['foo' => 'bar'];

        $picker->pickHttpAdapter($unsupportedClient);
    }
}
