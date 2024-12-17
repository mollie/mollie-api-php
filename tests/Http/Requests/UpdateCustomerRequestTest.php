<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\UpdateCustomerPayload;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;
use Mollie\Api\Resources\Customer;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class UpdateCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_customer()
    {
        $client = new MockClient([
            UpdateCustomerRequest::class => new MockResponse(200, 'customer'),
        ]);

        $request = new UpdateCustomerRequest('cst_kEn1PlbGa', new UpdateCustomerPayload(
            'Updated Customer Name',
            'updated@example.com',
        ));

        /** @var Customer */
        $customer = $client->send($request);

        $this->assertTrue($customer->getResponse()->successful());
        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateCustomerRequest('cst_kEn1PlbGa', new UpdateCustomerPayload(
            'Updated Customer Name',
            'updated@example.com',
        ));

        $this->assertEquals('customers/cst_kEn1PlbGa', $request->resolveResourcePath());
    }
}
