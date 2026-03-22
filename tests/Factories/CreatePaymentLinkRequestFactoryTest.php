<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentLinkRequestFactory;
use Mollie\Api\Http\Data\DateTime;
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
}
