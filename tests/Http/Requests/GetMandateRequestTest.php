<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Mandate;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetMandateRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_mandate()
    {
        $client = new MockClient([
            GetMandateRequest::class => new MockResponse(200, 'mandate'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $mandateId = 'mdt_h3gAaD5zP';
        $request = new GetMandateRequest($customerId, $mandateId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Mandate::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $mandateId = 'mdt_h3gAaD5zP';
        $request = new GetMandateRequest($customerId, $mandateId);

        $this->assertEquals(
            "customers/{$customerId}/mandates/{$mandateId}",
            $request->resolveResourcePath()
        );
    }
}
