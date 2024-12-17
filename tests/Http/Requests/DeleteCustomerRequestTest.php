<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class DeleteCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_customer()
    {
        $client = new MockClient([
            DeleteCustomerRequest::class => new MockResponse(204),
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
