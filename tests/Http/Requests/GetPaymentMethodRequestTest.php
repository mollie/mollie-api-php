<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\GetPaymentMethodQuery;
use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Method;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_method()
    {
        $client = new MockClient([
            GetPaymentMethodRequest::class => new MockResponse(200, 'method'),
        ]);

        $request = new GetPaymentMethodRequest('ideal');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Method */
        $method = $response->toResource();

        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('method', $method->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $methodId = 'ideal';
        $request = new GetPaymentMethodRequest($methodId);

        $this->assertEquals("methods/{$methodId}", $request->resolveResourcePath());
    }
}
