<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedInvoiceRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedInvoiceRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_paginated_invoice_request_object_with_full_data()
    {
        $request = GetPaginatedInvoiceRequestFactory::new()
            ->withQuery([
                'from' => 'inv_12345',
                'limit' => 50,
                'reference' => 'INV2024-001',
                'year' => '2024',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedInvoiceRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_invoice_request_object_with_minimal_data()
    {
        $request = GetPaginatedInvoiceRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetPaginatedInvoiceRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_invoice_request_object_with_partial_data()
    {
        $request = GetPaginatedInvoiceRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'year' => '2024',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedInvoiceRequest::class, $request);
    }
}
