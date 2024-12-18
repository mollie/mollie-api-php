<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class RevokeMandateRequestTest extends TestCase
{
    /** @test */
    public function it_can_revoke_mandate()
    {
        $client = new MockClient([
            RevokeMandateRequest::class => new MockResponse(204, ''),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $mandateId = 'mdt_h3gAaD5zP';
        $request = new RevokeMandateRequest($customerId, $mandateId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $mandateId = 'mdt_h3gAaD5zP';
        $request = new RevokeMandateRequest($customerId, $mandateId);

        $this->assertEquals(
            "customers/{$customerId}/mandates/{$mandateId}",
            $request->resolveResourcePath()
        );
    }
}
