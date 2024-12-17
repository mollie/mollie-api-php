<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\UpdateProfilePayload;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Resources\Profile;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

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

        /** @var Profile */
        $profile = $client->send($request);

        $this->assertTrue($profile->getResponse()->successful());
        $this->assertInstanceOf(Profile::class, $profile);
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
