<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Response;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class DeleteProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_profile()
    {
        $client = new MockClient([
            DeleteProfileRequest::class => new MockResponse(204, ''),
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
