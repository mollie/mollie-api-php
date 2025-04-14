<?php

namespace Tests\Http\Adapter;

use GuzzleHttp\Client as GuzzleClient;
use Mollie\Api\Exceptions\UnrecognizedClientException;
use Mollie\Api\Fake\MockMollieHttpAdapter;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Adapter\MollieHttpAdapterPicker;
use PHPUnit\Framework\TestCase;

class MollieHttpAdapterPickerTest extends TestCase
{
    /** @test */
    public function creates_a_guzzle_adapter_if_null_is_passed_and_guzzle_is_detected()
    {
        $picker = new MollieHttpAdapterPicker;

        $adapter = $picker->pickHttpAdapter(null);

        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $adapter);
    }

    /** @test */
    public function returns_the_adapter_that_was_passed_in()
    {
        $picker = new MollieHttpAdapterPicker;
        $mockAdapter = new MockMollieHttpAdapter;

        $adapter = $picker->pickHttpAdapter($mockAdapter);

        $this->assertInstanceOf(MockMollieHttpAdapter::class, $adapter);
        $this->assertEquals($mockAdapter, $adapter);
    }

    /** @test */
    public function wraps_a_guzzle_client_into_an_adapter()
    {
        $picker = new MollieHttpAdapterPicker;
        $guzzleClient = new GuzzleClient;

        $adapter = $picker->pickHttpAdapter($guzzleClient);

        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $adapter);
    }

    /** @test */
    public function throws_an_exception_when_receiving_an_unrecognized_client()
    {
        $this->expectExceptionObject(new UnrecognizedClientException('The provided http client or adapter was not recognized'));
        $picker = new MollieHttpAdapterPicker;
        $unsupportedClient = (object) ['foo' => 'bar'];

        $picker->pickHttpAdapter($unsupportedClient);
    }
}
