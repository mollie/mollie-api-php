<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreateCustomerPayload;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Api\Resources\Customer;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class CreateCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_customer()
    {
        $client = new MockMollieClient([
            CreateCustomerRequest::class => new MockResponse(201, 'customer'),
        ]);

        $payload = new CreateCustomerPayload(
            'John Doe',
            'john@example.org'
        );

        $request = new CreateCustomerRequest($payload);

        /** @var Customer */
        $customer = $client->send($request);

        $this->assertTrue($customer->getResponse()->successful());
        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateCustomerRequest(new CreateCustomerPayload(
            'John Doe',
            'john@example.org'
        ));

        $this->assertEquals('customers', $request->resolveResourcePath());
    }
}
