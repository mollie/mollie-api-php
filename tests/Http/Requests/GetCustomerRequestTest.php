<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Resources\Customer;
use PHPUnit\Framework\TestCase;

class GetCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_customer()
    {
        $client = new MockMollieClient([
            GetCustomerRequest::class => MockResponse::ok('customer'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $request = new GetCustomerRequest($customerId);

        /** @var Customer */
        $customer = $client->send($request);

        $this->assertTrue($customer->getResponse()->successful());
        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetCustomerRequest($customerId);

        $this->assertEquals(
            "customers/{$customerId}",
            $request->resolveResourcePath()
        );
    }
}
