<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Customer;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_customer()
    {
        $client = new MockClient([
            GetCustomerRequest::class => new MockResponse(200, 'customer'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $request = new GetCustomerRequest($customerId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Customer::class, $response->toResource());
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
