<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;
use Mollie\Api\Resources\Customer;
use PHPUnit\Framework\TestCase;

class UpdateCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_customer()
    {
        $client = new MockMollieClient([
            UpdateCustomerRequest::class => MockResponse::ok('customer'),
        ]);

        $request = new UpdateCustomerRequest('cst_kEn1PlbGa', 'Updated Customer Name', 'updated@example.com');

        /** @var Customer */
        $customer = $client->send($request);

        $this->assertTrue($customer->getResponse()->successful());
        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateCustomerRequest('cst_kEn1PlbGa', 'Updated Customer Name', 'updated@example.com');

        $this->assertEquals('customers/cst_kEn1PlbGa', $request->resolveResourcePath());
    }
}
