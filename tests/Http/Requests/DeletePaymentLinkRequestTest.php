<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeletePaymentLinkRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DeletePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_payment_link()
    {
        $client = new MockMollieClient([
            DeletePaymentLinkRequest::class => MockResponse::noContent(),
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
