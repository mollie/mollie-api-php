<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Resources\Mandate;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

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

        /** @var Mandate */
        $mandate = $client->send($request);

        $this->assertTrue($mandate->getResponse()->successful());
        $this->assertInstanceOf(Mandate::class, $mandate);
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
