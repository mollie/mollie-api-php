<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreateMandatePayload;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Mandate;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateMandateRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_mandate()
    {
        $client = new MockClient([
            CreateMandateRequest::class => new MockResponse(201, 'mandate'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $payload = new CreateMandatePayload(
            'directdebit',
            'John Doe',
            'NL55INGB0000000000'
        );

        $request = new CreateMandateRequest($customerId, $payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Mandate::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $payload = new CreateMandatePayload(
            'directdebit',
            'John Doe',
            'NL55INGB0000000000'
        );

        $request = new CreateMandateRequest($customerId, $payload);

        $this->assertEquals(
            "customers/{$customerId}/mandates",
            $request->resolveResourcePath()
        );
    }
}
