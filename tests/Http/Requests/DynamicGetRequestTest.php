<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class DynamicGetRequestTest extends TestCase
{
    /** @test */
    public function it_can_make_dynamic_get_request()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, 'payment'),
        ]);

        $request = new DynamicGetRequest(
            'payments/tr_WDqYK6vllg',
            Payment::class,
            ['testmode' => 'true']
        );

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Payment::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $path = 'payments/tr_WDqYK6vllg';
        $request = new DynamicGetRequest($path, Payment::class);

        $this->assertEquals($path, $request->resolveResourcePath());
    }
}
