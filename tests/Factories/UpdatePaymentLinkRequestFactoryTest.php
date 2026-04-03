<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdatePaymentLinkRequestFactory;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use PHPUnit\Framework\TestCase;

class UpdatePaymentLinkRequestFactoryTest extends TestCase
{
    private const PAYMENT_LINK_ID = 'pl_12345';

    /** @test */
    public function create_returns_update_payment_link_request_object_with_full_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
                'archived' => true,
                'allowedMethods' => ['ideal', 'creditcard'],
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_link_request_object_with_minimal_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_link_request_object_with_partial_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
                'archived' => true,
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function it_maps_lines_billing_and_shipping_address_and_minimum_amount()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Klarna order',
                'minimumAmount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'lines' => [
                    [
                        'description' => 'Bicycle tire',
                        'quantity' => 2,
                        'unitPrice' => ['currency' => 'EUR', 'value' => '12.48'],
                        'totalAmount' => ['currency' => 'EUR', 'value' => '24.95'],
                        'vatRate' => '21.00',
                        'vatAmount' => ['currency' => 'EUR', 'value' => '4.34'],
                    ],
                ],
                'billingAddress' => [
                    'givenName' => 'John',
                    'familyName' => 'Doe',
                    'email' => 'john.doe@example.org',
                    'streetAndNumber' => 'Keizersgracht 126',
                    'postalCode' => '1015 CW',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
                'shippingAddress' => [
                    'givenName' => 'Jane',
                    'familyName' => 'Doe',
                    'email' => 'jane.doe@example.org',
                    'streetAndNumber' => 'Herengracht 182',
                    'postalCode' => '1016 BS',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);

        $payload = $request->payload()->all();

        $this->assertInstanceOf(Money::class, $payload['minimumAmount']);
        $this->assertEquals('EUR', $payload['minimumAmount']->currency);
        $this->assertEquals('10.00', $payload['minimumAmount']->value);

        $this->assertInstanceOf(DataCollection::class, $payload['lines']);
        $this->assertCount(1, $payload['lines']);

        $this->assertInstanceOf(Address::class, $payload['billingAddress']);
        $this->assertEquals('John', $payload['billingAddress']->givenName);
        $this->assertEquals('NL', $payload['billingAddress']->country);

        $this->assertInstanceOf(Address::class, $payload['shippingAddress']);
        $this->assertEquals('Amsterdam', $payload['shippingAddress']->city);
    }

    /** @test */
    public function it_returns_null_for_optional_klarna_fields_when_not_provided()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
            ])
            ->create();

        $payload = $request->payload()->all();

        $this->assertNull($payload['lines']);
        $this->assertNull($payload['billingAddress']);
        $this->assertNull($payload['shippingAddress']);
        $this->assertNull($payload['minimumAmount']);
    }
}
