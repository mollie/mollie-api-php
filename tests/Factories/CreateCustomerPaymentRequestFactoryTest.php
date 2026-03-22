<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateCustomerPaymentRequestFactory;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Types\PaymentQuery;
use PHPUnit\Framework\TestCase;

class CreateCustomerPaymentRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    /** @test */
    public function create_returns_customer_payment_request_object_with_full_data()
    {
        $request = CreateCustomerPaymentRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'cancelUrl' => 'https://example.com/cancel',
                'webhookUrl' => 'https://example.com/webhook',
                'lines' => [
                    [
                        'description' => 'Product A',
                        'quantity' => 2,
                        'vatRate' => '21.00',
                        'unitPrice' => [
                            'currency' => 'EUR',
                            'value' => '50.00',
                        ],
                        'totalAmount' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                    ],
                ],
                'billingAddress' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
                'shippingAddress' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
                'locale' => 'nl_NL',
                'method' => 'ideal',
                'issuer' => 'ideal_INGBNL2A',
                'restrictPaymentMethodsToCountry' => 'NL',
                'metadata' => [
                    'order_id' => '12345',
                ],
                'captureMode' => 'manual',
                'captureDelay' => 'P3D',
                'applicationFee' => [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => '1.00',
                    ],
                    'description' => 'Application fee',
                ],
                'routing' => [
                    [
                        'amount' => [
                            'currency' => 'EUR',
                            'value' => '50.00',
                        ],
                        'destination' => [
                            'type' => 'organization',
                            'organizationId' => 'org_12345',
                        ],
                    ],
                ],
                'sequenceType' => 'first',
                'mandateId' => 'mdt_12345',
                'profileId' => 'pfl_12345',
                'additional' => [
                    'customField' => 'customValue',
                ],
            ])
            ->withQuery([
                'include' => [PaymentQuery::INCLUDE_QR_CODE],
            ])
            ->create();

        $this->assertInstanceOf(CreateCustomerPaymentRequest::class, $request);
        $this->assertEquals(PaymentQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_customer_payment_request_object_with_minimal_data()
    {
        $request = CreateCustomerPaymentRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
            ])
            ->create();

        $this->assertInstanceOf(CreateCustomerPaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_customer_payment_request_object_with_partial_data()
    {
        $request = CreateCustomerPaymentRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'webhookUrl' => 'https://example.com/webhook',
                'metadata' => [
                    'order_id' => '12345',
                ],
                'method' => 'ideal',
                'sequenceType' => 'first',
            ])
            ->create();

        $this->assertInstanceOf(CreateCustomerPaymentRequest::class, $request);
    }
}
