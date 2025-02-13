<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Resources\Profile;
use PHPUnit\Framework\TestCase;

class UpdateProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_profile()
    {
        $client = new MockMollieClient([
            UpdateProfileRequest::class => MockResponse::ok('profile'),
        ]);

        $request = new UpdateProfileRequest('pfl_v9hTwCvYqw', 'Updated Profile Name');

        /** @var Profile */
        $profile = $client->send($request);

        $this->assertTrue($profile->getResponse()->successful());
        $this->assertInstanceOf(Profile::class, $profile);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateProfileRequest('pfl_v9hTwCvYqw', 'Updated Profile Name');

        $this->assertEquals('profiles/pfl_v9hTwCvYqw', $request->resolveResourcePath());
    }
}
