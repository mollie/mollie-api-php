<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetPaymentMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_method()
    {
        $client = new MockClient([
            GetPaymentMethodRequest::class => new MockResponse(200, 'method'),
        ]);

        $request = new GetPaymentMethodRequest('ideal');

        /** @var Method */
        $method = $client->send($request);

        $this->assertTrue($method->getResponse()->successful());
        $this->assertInstanceOf(Method::class, $method);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $methodId = 'ideal';
        $request = new GetPaymentMethodRequest($methodId);

        $this->assertEquals("methods/{$methodId}", $request->resolveResourcePath());
    }
}
