<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreateCustomerPayload;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Customer;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_customer()
    {
        $client = new MockClient([
            CreateCustomerRequest::class => new MockResponse(201, 'customer'),
        ]);

        $payload = new CreateCustomerPayload(
            'John Doe',
            'john@example.org'
        );

        $request = new CreateCustomerRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Customer::class, $response->toResource());
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
