<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\CreatePaymentQuery;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateCustomerPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_customer_payment()
    {
        $client = new MockClient([
            CreateCustomerPaymentRequest::class => new MockResponse(201, 'payment'),
        ]);

        $payload = new CreatePaymentPayload(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect'
        );

        $request = new CreateCustomerPaymentRequest(
            'cst_123',
            $payload,
            new CreatePaymentQuery(true)
        );

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Payment::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_123';
        $request = new CreateCustomerPaymentRequest($customerId, new CreatePaymentPayload(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect'
        ));

        $this->assertEquals("customers/{$customerId}/payments", $request->resolveResourcePath());
    }
}
