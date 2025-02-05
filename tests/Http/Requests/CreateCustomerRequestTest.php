<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Api\Resources\Customer;
use PHPUnit\Framework\TestCase;

class CreateCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_customer()
    {
        $client = new MockMollieClient([
            CreateCustomerRequest::class => MockResponse::created('customer'),
        ]);

        $request = new CreateCustomerRequest(
            'John Doe',
            'john@example.org'
        );

        /** @var Customer */
        $customer = $client->send($request);

        $this->assertTrue($customer->getResponse()->successful());
        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateCustomerRequest(
            'John Doe',
            'john@example.org'
        );

        $this->assertEquals('customers', $request->resolveResourcePath());
    }
}
