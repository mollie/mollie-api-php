<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateSalesInvoiceRequestFactory;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;
use Mollie\Api\Types\PaymentTerm;
use PHPUnit\Framework\TestCase;

class CreateSalesInvoiceRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_sales_invoice_request_object_with_full_data()
    {
        $request = CreateSalesInvoiceRequestFactory::new()
            ->withPayload([
                'currency' => 'EUR',
                'status' => 'draft',
                'vatScheme' => 'standard',
                'vatMode' => 'inclusive',
                'paymentTerm' => 30,
                'recipientIdentifier' => 'cst_12345',
                'recipient' => [
                    'type' => 'organization',
                    'email' => 'org@example.com',
                    'streetAndNumber' => 'Business Ave 42',
                    'postalCode' => '5678CD',
                    'city' => 'Rotterdam',
                    'country' => 'NL',
                    'locale' => 'nl_NL',
                ],
                'lines' => [
                    [
                        'description' => 'Product A',
                        'quantity' => 2,
                        'vatRate' => '21.00',
                        'unitPrice' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                    ],
                ],
                'profileId' => 'pfl_12345',
                'memo' => 'Please pay within 30 days',
                'paymentDetails' => [
                    'source' => 'NL55INGB0000000000',
                    'sourceDescription' => 'ING Bank',
                ],
                'emailDetails' => [
                    'subject' => 'Invoice from Example Company',
                    'body' => 'Please find attached the invoice for your recent purchase.',
                ],
                'webhookUrl' => 'https://example.com/webhook',
                'discount' => [
                    'type' => 'percentage',
                    'value' => '10',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateSalesInvoiceRequest::class, $request);
    }

    /** @test */
    public function create_returns_sales_invoice_request_object_with_minimal_data()
    {
        $request = CreateSalesInvoiceRequestFactory::new()
            ->withPayload([
                'currency' => 'EUR',
                'status' => 'draft',
                'vatScheme' => 'standard',
                'vatMode' => 'inclusive',
                'recipientIdentifier' => 'cst_12345',
                'paymentTerm' => PaymentTerm::DAYS_30,
                'recipient' => [
                    'type' => 'organization',
                    'email' => 'org@example.com',
                    'streetAndNumber' => 'Business Ave 42',
                    'postalCode' => '5678CD',
                    'city' => 'Rotterdam',
                    'country' => 'NL',
                    'locale' => 'nl_NL',
                ],
                'lines' => [
                    [
                        'description' => 'Product A',
                        'quantity' => 1,
                        'vatRate' => '21.00',
                        'unitPrice' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                    ],
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateSalesInvoiceRequest::class, $request);
    }

    /** @test */
    public function create_returns_sales_invoice_request_object_with_partial_data()
    {
        $request = CreateSalesInvoiceRequestFactory::new()
            ->withPayload([
                'currency' => 'EUR',
                'status' => 'draft',
                'vatScheme' => 'standard',
                'vatMode' => 'inclusive',
                'paymentTerm' => 30,
                'recipientIdentifier' => 'cst_12345',
                'recipient' => [
                    'type' => 'organization',
                    'email' => 'org@example.com',
                    'streetAndNumber' => 'Business Ave 42',
                    'postalCode' => '5678CD',
                    'city' => 'Rotterdam',
                    'country' => 'NL',
                    'locale' => 'nl_NL',
                ],
                'lines' => [
                    [
                        'description' => 'Product A',
                        'quantity' => 1,
                        'vatRate' => '21.00',
                        'unitPrice' => [
                            'currency' => 'EUR',
                            'value' => '100.00',
                        ],
                    ],
                ],
                'memo' => 'Please pay within 30 days',
                'webhookUrl' => 'https://example.com/webhook',
            ])
            ->create();

        $this->assertInstanceOf(CreateSalesInvoiceRequest::class, $request);
    }
}
