<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateSessionRequestFactory;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use PHPUnit\Framework\TestCase;

class CreateSessionRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_session_request_object_with_full_data()
    {
        $request = CreateSessionRequestFactory::new()
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'description' => 'Order #12345',
                'redirectUrl' => 'https://example.com/redirect',
                'cancelUrl' => 'https://example.com/cancel',
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
                'customerId' => 'cst_12345',
                'sequenceType' => 'oneoff',
                'metadata' => [
                    'order_id' => '12345',
                ],
                'payment' => [
                    'webhookUrl' => 'https://example.com/webhook',
                ],
                'profileId' => 'pfl_12345',
                'testmode' => true,
            ])
            ->create();

        $this->assertInstanceOf(CreateSessionRequest::class, $request);
    }

    /** @test */
    public function create_returns_session_request_object_with_minimal_data()
    {
        $request = CreateSessionRequestFactory::new()
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'description' => 'Order #12345',
                'redirectUrl' => 'https://example.com/redirect',
                'lines' => [
                    [
                        'description' => 'Product A',
                        'quantity' => 1,
                        'unitPrice' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                        'totalAmount' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                    ],
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateSessionRequest::class, $request);
    }
}
