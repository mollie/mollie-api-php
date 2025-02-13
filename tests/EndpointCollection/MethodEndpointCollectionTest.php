<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Http\Requests\GetMethodRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use PHPUnit\Framework\TestCase;

class MethodEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetMethodRequest::class => MockResponse::ok('method', 'ideal'),
        ]);

        /** @var Method $method */
        $method = $client->methods->get('ideal');

        $this->assertMethod($method);
    }

    /** @test */
    public function all()
    {
        $client = new MockMollieClient([
            GetAllMethodsRequest::class => MockResponse::ok('method-list'),
        ]);

        /** @var MethodCollection $methods */
        $methods = $client->methods->all();

        $this->assertInstanceOf(MethodCollection::class, $methods);
        $this->assertEquals(2, $methods->count());
        $this->assertCount(2, $methods);

        foreach ($methods as $method) {
            $this->assertInstanceOf(Method::class, $method);
            $this->assertNotEmpty($method->id);
            $this->assertNotEmpty($method->description);
            $this->assertNotEmpty($method->status);
        }
    }

    /** @test */
    public function all_enabled()
    {
        $client = new MockMollieClient([
            GetEnabledMethodsRequest::class => MockResponse::ok('method-list'),
        ]);

        /** @var MethodCollection $methods */
        $methods = $client->methods->allEnabled();

        $this->assertInstanceOf(MethodCollection::class, $methods);
        $this->assertEquals(2, $methods->count());
        $this->assertCount(2, $methods);
    }

    protected function assertMethod(Method $method)
    {
        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('method', $method->resource);
        $this->assertEquals('iDEAL', $method->description);
        $this->assertEquals('0.01', $method->minimumAmount->value);
        $this->assertEquals('EUR', $method->minimumAmount->currency);
        $this->assertEquals('50000.00', $method->maximumAmount->value);
        $this->assertEquals('activated', $method->status);
        $this->assertNotEmpty($method->image);
    }
}
