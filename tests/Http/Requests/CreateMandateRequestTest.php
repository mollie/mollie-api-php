<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\CreateMandatePayload;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Resources\Mandate;
use PHPUnit\Framework\TestCase;

class CreateMandateRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_mandate()
    {
        $client = new MockMollieClient([
            CreateMandateRequest::class => new MockResponse(201, 'mandate'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $payload = new CreateMandatePayload(
            'directdebit',
            'John Doe',
            'NL55INGB0000000000'
        );

        $request = new CreateMandateRequest($customerId, $payload);

        /** @var Mandate */
        $mandate = $client->send($request);

        $this->assertTrue($mandate->getResponse()->successful());
        $this->assertInstanceOf(Mandate::class, $mandate);
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
