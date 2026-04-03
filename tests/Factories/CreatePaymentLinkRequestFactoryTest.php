<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentLinkRequestFactory;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use PHPUnit\Framework\TestCase;

class CreatePaymentLinkRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_payment_link_request_object_with_full_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'webhookUrl' => 'https://example.com/webhook',
                'profileId' => 'pfl_12345',
                'reusable' => true,
                'expiresAt' => '2024-12-31',
                'allowedMethods' => ['ideal', 'creditcard'],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_link_request_object_with_minimal_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_link_request_object_with_partial_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'webhookUrl' => 'https://example.com/webhook',
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function it_handles_date_string_without_time_information()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'expiresAt' => '2024-12-31',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);

        $payload = $request->payload()->all();
        $this->assertInstanceOf(DateTime::class, $payload['expiresAt']);
        $this->assertEquals('2024-12-31T00:00:00+00:00', (string) $payload['expiresAt']);
    }

    /** @test */
    public function it_handles_date_string_without_timezone_information()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'expiresAt' => '2024-12-31T12:34:56',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);

        $payload = $request->payload()->all();
        $this->assertInstanceOf(DateTime::class, $payload['expiresAt']);
        $this->assertEquals('2024-12-31T12:34:56+00:00', (string) $payload['expiresAt']);
    }

    /** @test */
    public function it_handles_complete_iso8601_date_string()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'expiresAt' => '2024-12-31T12:34:56+02:00',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);

        $payload = $request->payload()->all();
        $this->assertInstanceOf(DateTime::class, $payload['expiresAt']);
        $this->assertEquals('2024-12-31T12:34:56+02:00', (string) $payload['expiresAt']);
    }

    /** @test */
    public function it_returns_null_when_expires_at_is_not_provided()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'redirectUrl' => 'https://example.com/redirect',
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);

        $payload = $request->payload()->all();
        $this->assertNull($payload['expiresAt']);
    }

    /** @test */
    public function it_maps_lines_billing_and_shipping_address_and_minimum_amount()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Klarna order',
                'redirectUrl' => 'https://example.com/redirect',
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
                    'givenName' => 'John',
                    'familyName' => 'Doe',
                    'email' => 'john.doe@example.org',
                    'streetAndNumber' => 'Keizersgracht 126',
                    'postalCode' => '1015 CW',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);

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
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'redirectUrl' => 'https://example.com/redirect',
            ])
            ->create();

        $payload = $request->payload()->all();

        $this->assertNull($payload['lines']);
        $this->assertNull($payload['billingAddress']);
        $this->assertNull($payload['shippingAddress']);
        $this->assertNull($payload['minimumAmount']);
    }
}
