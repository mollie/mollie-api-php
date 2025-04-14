<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DeleteProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_profile()
    {
        $client = new MockMollieClient([
            DeleteProfileRequest::class => MockResponse::noContent(),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $request = new DeleteProfileRequest($profileId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $profileId = 'pfl_v9hTwCvYqw';
        $request = new DeleteProfileRequest($profileId);

        $this->assertEquals(
            "profiles/{$profileId}",
            $request->resolveResourcePath()
        );
    }
}
