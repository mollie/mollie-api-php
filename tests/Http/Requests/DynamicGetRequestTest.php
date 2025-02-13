<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class DynamicGetRequestTest extends TestCase
{
    /** @test */
    public function it_can_make_dynamic_get_request()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment'),
        ]);

        $request = new DynamicGetRequest(
            'payments/tr_WDqYK6vllg',
            ['testmode' => 'true']
        );

        $request->setHydratableResource(Payment::class);

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $path = 'payments/tr_WDqYK6vllg';
        $request = new DynamicGetRequest($path);
        $request->setHydratableResource(Payment::class);

        $this->assertEquals($path, $request->resolveResourcePath());
    }
}
