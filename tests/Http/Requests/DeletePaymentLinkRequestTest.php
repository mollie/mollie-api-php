<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DeletePaymentLinkRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class DeletePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_payment_link()
    {
        $client = new MockClient([
            DeletePaymentLinkRequest::class => new MockResponse(204),
        ]);

        $request = new DeletePaymentLinkRequest('pl_123');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new DeletePaymentLinkRequest('pl_123');

        $this->assertEquals('payment-links/pl_123', $request->resolveResourcePath());
    }
}
