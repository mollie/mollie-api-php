<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class MethodEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetPaymentMethodRequest::class => new MockResponse(200, 'method', 'ideal'),
        ]);

        /** @var Method $method */
        $method = $client->methods->get('ideal');

        $this->assertMethod($method);
    }

    /** @test */
    public function all()
    {
        $client = new MockClient([
            GetAllMethodsRequest::class => new MockResponse(200, 'method-list'),
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
        $client = new MockClient([
            GetEnabledMethodsRequest::class => new MockResponse(200, 'method-list'),
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
