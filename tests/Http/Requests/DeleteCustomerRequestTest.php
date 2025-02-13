<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DeleteCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_customer()
    {
        $client = new MockMollieClient([
            DeleteCustomerRequest::class => MockResponse::noContent(),
        ]);

        $request = new DeleteCustomerRequest('cst_123');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new DeleteCustomerRequest('cst_123');

        $this->assertEquals('customers/cst_123', $request->resolveResourcePath());
    }
}
