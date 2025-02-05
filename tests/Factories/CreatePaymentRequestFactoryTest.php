<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentRequestFactory;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use PHPUnit\Framework\TestCase;

class CreatePaymentRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_payment_request_object_with_full_data()
    {
        $request = CreatePaymentRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00'
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
                            'value' => '50.00'
                        ],
                        'totalAmount' => [
                            'currency' => 'EUR',
                            'value' => '100.00'
                        ]
                    ]
                ],
                'billingAddress' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL'
                ],
                'shippingAddress' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL'
                ],
                'locale' => 'nl_NL',
                'method' => 'ideal',
                'issuer' => 'ideal_INGBNL2A',
                'restrictPaymentMethodsToCountry' => 'NL',
                'metadata' => [
                    'order_id' => '12345'
                ],
                'captureMode' => 'manual',
                'captureDelay' => 'P3D',
                'applicationFee' => [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => '1.00'
                    ],
                    'description' => 'Application fee'
                ],
                'routing' => [
                    [
                        'amount' => [
                            'currency' => 'EUR',
                            'value' => '50.00'
                        ],
                        'destination' => [
                            'type' => 'organization',
                            'organizationId' => 'org_12345'
                        ]
                    ]
                ],
                'sequenceType' => 'oneoff',
                'mandateId' => 'mdt_12345',
                'customerId' => 'cst_12345',
                'profileId' => 'pfl_12345',
                'additional' => [
                    'customField' => 'customValue'
                ]
            ])
            ->withQuery([
                'includeQrCode' => true
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_request_object_with_minimal_data()
    {
        $request = CreatePaymentRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00'
                ],
                'redirectUrl' => 'https://example.com/redirect'
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentRequest::class, $request);
    }
}
