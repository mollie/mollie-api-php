<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;
use PHPUnit\Framework\TestCase;

class CustomerEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateCustomerRequest::class => MockResponse::created('customer'),
        ]);

        /** @var Customer $customer */
        $customer = $client->customers->create([
            'name' => 'John Doe',
            'email' => 'john@example.org',
        ]);

        $this->assertCustomer($customer);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetCustomerRequest::class => MockResponse::ok('customer'),
        ]);

        /** @var Customer $customer */
        $customer = $client->customers->get('cst_kEn1PlbGa');

        $this->assertCustomer($customer);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdateCustomerRequest::class => MockResponse::ok('customer'),
        ]);

        /** @var Customer $customer */
        $customer = $client->customers->update('cst_kEn1PlbGa', [
            'name' => 'Updated Name',
            'email' => 'updated@example.org',
        ]);

        $this->assertCustomer($customer);
    }

    /** @test */
    public function delete()
    {
        $client = new MockMollieClient([
            DeleteCustomerRequest::class => MockResponse::noContent(),
        ]);

        $client->customers->delete('cst_kEn1PlbGa');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerRequest::class => MockResponse::ok('customer-list'),
        ]);

        /** @var CustomerCollection $customers */
        $customers = $client->customers->page();

        $this->assertInstanceOf(CustomerCollection::class, $customers);
        $this->assertGreaterThan(0, $customers->count());

        foreach ($customers as $customer) {
            $this->assertCustomer($customer);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerRequest::class => MockResponse::ok('customer-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'customers'),
        ]);

        foreach ($client->customers->iterator() as $customer) {
            $this->assertCustomer($customer);
        }
    }

    protected function assertCustomer(Customer $customer)
    {
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('customer', $customer->resource);
        $this->assertNotEmpty($customer->id);
        $this->assertNotEmpty($customer->mode);
        $this->assertNotEmpty($customer->name);
        $this->assertNotEmpty($customer->email);
        $this->assertNotEmpty($customer->createdAt);
        $this->assertNotEmpty($customer->_links);
    }
}
