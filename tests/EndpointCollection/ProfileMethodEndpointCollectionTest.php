<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DisableProfileMethodRequest;
use Mollie\Api\Http\Requests\EnableProfileMethodRequest;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;

class ProfileMethodEndpointCollectionTest extends TestCase
{
    /** @test */
    public function enable_for_id()
    {
        $client = new MockMollieClient([
            EnableProfileMethodRequest::class => new MockResponse(200, 'method', 'ideal'),
        ]);

        /** @var Method $method */
        $method = $client->profileMethods->enableForId('pfl_v9hTwCvYqw', 'ideal');

        $this->assertMethod($method);
    }

    /** @test */
    public function enable()
    {
        $client = new MockMollieClient([
            EnableProfileMethodRequest::class => new MockResponse(200, 'method', 'ideal'),
        ]);

        /** @var Method $method */
        $method = $client->profileMethods->enable('ideal');

        $this->assertMethod($method);
    }

    /** @test */
    public function disable_for_id()
    {
        $client = new MockMollieClient([
            DisableProfileMethodRequest::class => new MockResponse(204),
        ]);

        $client->profileMethods->disableForId('pfl_v9hTwCvYqw', 'ideal');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function disable()
    {
        $client = new MockMollieClient([
            DisableProfileMethodRequest::class => new MockResponse(204),
        ]);

        $client->profileMethods->disable('ideal');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    protected function assertMethod(Method $method)
    {
        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('method', $method->resource);
        $this->assertNotEmpty($method->id);
        $this->assertNotEmpty($method->description);
        $this->assertNotEmpty($method->status);
        $this->assertNotEmpty($method->_links);
    }
}
