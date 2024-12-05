<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\UpdateProfilePayload;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Profile;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdateProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_profile()
    {
        $client = new MockClient([
            UpdateProfileRequest::class => new MockResponse(200, 'profile'),
        ]);

        $request = new UpdateProfileRequest('pfl_v9hTwCvYqw', new UpdateProfilePayload(
            'Updated Profile Name',
        ));

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Profile */
        $profile = $response->toResource();

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('profile', $profile->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateProfileRequest('pfl_v9hTwCvYqw', new UpdateProfilePayload(
            'Updated Profile Name',
        ));

        $this->assertEquals('profiles/pfl_v9hTwCvYqw', $request->resolveResourcePath());
    }
}
