<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdateSalesInvoiceRequestFactory;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;
use PHPUnit\Framework\TestCase;

class UpdateSalesInvoiceRequestFactoryTest extends TestCase
{
    private const INVOICE_ID = 'inv_12345';

    /** @test */
    public function create_returns_update_sales_invoice_request_object_with_full_data()
    {
        $request = UpdateSalesInvoiceRequestFactory::new(self::INVOICE_ID)
            ->withPayload([
                'status' => 'draft',
                'recipientIdentifier' => 'cst_12345',
                'paymentTerm' => 30,
                'memo' => 'Please pay within 30 days',
                'paymentDetails' => [
                    'source' => 'NL55INGB0000000000',
                    'sourceDescription' => 'ING Bank',
                ],
                'emailDetails' => [
                    'subject' => 'Invoice from Example Company',
                    'body' => 'Please find attached the invoice for your recent purchase.',
                ],
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
                'webhookUrl' => 'https://example.com/webhook',
                'discount' => [
                    'type' => 'percentage',
                    'value' => '10',
                ],
            ])
            ->create();

        $this->assertInstanceOf(UpdateSalesInvoiceRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_sales_invoice_request_object_with_minimal_data()
    {
        $request = UpdateSalesInvoiceRequestFactory::new(self::INVOICE_ID)
            ->withPayload([
                'status' => 'draft',
                'recipientIdentifier' => 'cst_12345',
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

        $this->assertInstanceOf(UpdateSalesInvoiceRequest::class, $request);
    }
}
