<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetMethodRequest;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;

class GetMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_method()
    {
        $client = new MockMollieClient([
            GetMethodRequest::class => MockResponse::ok('method'),
        ]);

        $request = new GetMethodRequest('ideal');

        /** @var Method */
        $method = $client->send($request);

        $this->assertTrue($method->getResponse()->successful());
        $this->assertInstanceOf(Method::class, $method);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $methodId = 'ideal';
        $request = new GetMethodRequest($methodId);

        $this->assertEquals("methods/{$methodId}", $request->resolveResourcePath());
    }
}
