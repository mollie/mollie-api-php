<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Invoice;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_invoice()
    {
        $client = new MockClient([
            GetInvoiceRequest::class => new MockResponse(200, 'invoice'),
        ]);

        $invoiceId = 'inv_xBEbP9rvAq';
        $request = new GetInvoiceRequest($invoiceId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Invoice::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetInvoiceRequest('inv_xBEbP9rvAq');

        $this->assertEquals('invoices/inv_xBEbP9rvAq', $request->resolveResourcePath());
    }
}
